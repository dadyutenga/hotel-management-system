<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Stocktake;
use App\Models\StocktakeItem;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Folio;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DirectorController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;
        $tenant = $user->tenant;

        $today = today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $reservations = Reservation::where('property_id', $property->id)
            ->whereBetween('check_in_date', [$startOfMonth, $endOfMonth])
            ->get();

        $payments = Payment::where('tenant_id', $tenant->id)
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->get();

        $stockValue = StockLevel::where('property_id', $property->id)
            ->with('item')
            ->get()
            ->sum(function($stock) {
                return $stock->quantity * ($stock->item->unit_cost ?? 0);
            });

        $pendingPOs = PurchaseOrder::where('property_id', $property->id)
            ->whereIn('status', ['DRAFT', 'APPROVED'])
            ->count();

        $lowStockCount = StockLevel::where('property_id', $property->id)
            ->whereHas('item', function($q) {
                $q->whereRaw('stock_levels.quantity <= items.reorder_level');
            })
            ->count();

        return response()->json([
            'success' => true,
            'dashboard' => [
                'reservations' => [
                    'total' => $reservations->count(),
                    'confirmed' => $reservations->where('status', 'CONFIRMED')->count(),
                    'checked_in' => $reservations->where('status', 'CHECKED_IN')->count(),
                    'checked_out' => $reservations->where('status', 'CHECKED_OUT')->count(),
                ],
                'payments' => [
                    'total_amount' => $payments->sum('amount'),
                    'count' => $payments->count(),
                ],
                'inventory' => [
                    'total_value' => $stockValue,
                    'pending_orders' => $pendingPOs,
                    'low_stock_items' => $lowStockCount,
                ],
            ]
        ]);
    }

    public function approvePurchaseOrder(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $purchaseOrder = PurchaseOrder::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order not found'
            ], 404);
        }

        if ($purchaseOrder->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft purchase orders can be approved'
            ], 400);
        }

        try {
            $purchaseOrder->update([
                'status' => 'APPROVED',
                'approved_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase order approved successfully',
                'purchase_order' => $purchaseOrder
            ]);

        } catch (\Exception $e) {
            \Log::error('Error approving purchase order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPurchaseOrder(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $purchaseOrder = PurchaseOrder::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order not found'
            ], 404);
        }

        if ($purchaseOrder->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft purchase orders can be rejected'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $purchaseOrder->update([
                'status' => 'CANCELLED',
                'notes' => $purchaseOrder->notes . "\n\nRejected by: " . $user->full_name . "\nReason: " . $request->reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase order rejected',
                'purchase_order' => $purchaseOrder
            ]);

        } catch (\Exception $e) {
            \Log::error('Error rejecting purchase order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pendingApprovals()
    {
        $user = Auth::user();
        $property = $user->property;

        $pendingPOs = PurchaseOrder::where('property_id', $property->id)
            ->where('status', 'DRAFT')
            ->with(['vendor', 'creator', 'purchaseOrderItems.item'])
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'pending_purchase_orders' => $pendingPOs,
            'count' => $pendingPOs->count()
        ]);
    }

    public function createStocktake(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|uuid|exists:warehouses,id',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $stocktake = Stocktake::create([
                'property_id' => $property->id,
                'warehouse_id' => $request->warehouse_id,
                'status' => 'DRAFT',
                'start_date' => $request->start_date,
                'notes' => $request->notes,
                'created_by' => $user->id,
            ]);

            $stockLevels = StockLevel::where('property_id', $property->id)
                ->where('warehouse_id', $request->warehouse_id)
                ->with('item')
                ->get();

            foreach ($stockLevels as $stockLevel) {
                StocktakeItem::create([
                    'stocktake_id' => $stocktake->id,
                    'item_id' => $stockLevel->item_id,
                    'system_quantity' => $stockLevel->quantity,
                    'counted_quantity' => null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stocktake created successfully',
                'stocktake' => $stocktake->load(['warehouse', 'stocktakeItems.item'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating stocktake: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating stocktake: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStocktakes(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $query = Stocktake::where('property_id', $property->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $query->with(['warehouse', 'creator', 'completedBy']);
        $query->orderBy('start_date', 'desc');

        $stocktakes = $query->paginate(20);

        return response()->json([
            'success' => true,
            'stocktakes' => $stocktakes
        ]);
    }

    public function showStocktake($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $stocktake = Stocktake::where('id', $id)
            ->where('property_id', $property->id)
            ->with([
                'warehouse',
                'creator',
                'completedBy',
                'stocktakeItems.item.category',
                'stocktakeItems.countedBy'
            ])
            ->first();

        if (!$stocktake) {
            return response()->json([
                'success' => false,
                'message' => 'Stocktake not found'
            ], 404);
        }

        $variances = $stocktake->stocktakeItems->filter(function($item) {
            return $item->counted_quantity !== null && $item->variance != 0;
        });

        return response()->json([
            'success' => true,
            'stocktake' => $stocktake,
            'summary' => [
                'total_items' => $stocktake->stocktakeItems->count(),
                'counted_items' => $stocktake->stocktakeItems->whereNotNull('counted_quantity')->count(),
                'variance_count' => $variances->count(),
                'total_variance_value' => $variances->sum(function($item) {
                    return $item->variance * ($item->item->unit_cost ?? 0);
                }),
            ]
        ]);
    }

    public function startStocktake($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $stocktake = Stocktake::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$stocktake) {
            return response()->json([
                'success' => false,
                'message' => 'Stocktake not found'
            ], 404);
        }

        if ($stocktake->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft stocktakes can be started'
            ], 400);
        }

        try {
            $stocktake->update(['status' => 'IN_PROGRESS']);

            return response()->json([
                'success' => true,
                'message' => 'Stocktake started',
                'stocktake' => $stocktake
            ]);

        } catch (\Exception $e) {
            \Log::error('Error starting stocktake: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error starting stocktake: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeStocktake(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $stocktake = Stocktake::where('id', $id)
            ->where('property_id', $property->id)
            ->with('stocktakeItems')
            ->first();

        if (!$stocktake) {
            return response()->json([
                'success' => false,
                'message' => 'Stocktake not found'
            ], 404);
        }

        if ($stocktake->status !== 'IN_PROGRESS') {
            return response()->json([
                'success' => false,
                'message' => 'Only in-progress stocktakes can be completed'
            ], 400);
        }

        $uncounteditems = $stocktake->stocktakeItems->whereNull('counted_quantity');
        if ($uncounteditems->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'All items must be counted before completing stocktake',
                'uncounted_count' => $uncounteditems->count()
            ], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($stocktake->stocktakeItems as $item) {
                if ($item->variance != 0) {
                    $stockLevel = StockLevel::where('property_id', $property->id)
                        ->where('warehouse_id', $stocktake->warehouse_id)
                        ->where('item_id', $item->item_id)
                        ->first();

                    if ($stockLevel) {
                        $stockLevel->update(['quantity' => $item->counted_quantity]);

                        StockMovement::create([
                            'property_id' => $property->id,
                            'item_id' => $item->item_id,
                            'warehouse_id' => $stocktake->warehouse_id,
                            'movement_type' => 'ADJUSTMENT',
                            'quantity' => $item->variance,
                            'unit_cost' => 0,
                            'reference_type' => 'STOCKTAKE',
                            'reference_id' => $stocktake->id,
                            'notes' => 'Stocktake adjustment - Variance: ' . $item->variance,
                            'movement_date' => now(),
                            'created_by' => $user->id,
                        ]);
                    }
                }
            }

            $stocktake->update([
                'status' => 'COMPLETED',
                'end_date' => now(),
                'completed_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stocktake completed and stock levels adjusted',
                'stocktake' => $stocktake
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error completing stocktake: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error completing stocktake: ' . $e->getMessage()
            ], 500);
        }
    }

    public function financialSummary(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $startDate = $request->get('start_date', today()->subDays(30));
        $endDate = $request->get('end_date', today());

        $payments = Payment::where('tenant_id', $tenant->id)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $reservations = Reservation::where('property_id', $property->id)
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->get();

        $purchaseOrders = PurchaseOrder::where('property_id', $property->id)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('status', ['APPROVED', 'SENT', 'RECEIVED'])
            ->get();

        return response()->json([
            'success' => true,
            'summary' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'revenue' => [
                    'total_payments' => $payments->sum('amount'),
                    'payment_count' => $payments->count(),
                    'by_method' => $payments->groupBy('payment_method')->map->sum('amount'),
                ],
                'reservations' => [
                    'total' => $reservations->count(),
                    'confirmed' => $reservations->where('status', 'CONFIRMED')->count(),
                    'checked_in' => $reservations->where('status', 'CHECKED_IN')->count(),
                    'checked_out' => $reservations->where('status', 'CHECKED_OUT')->count(),
                ],
                'expenses' => [
                    'purchase_orders' => $purchaseOrders->sum('total_amount'),
                    'po_count' => $purchaseOrders->count(),
                ],
                'net_position' => $payments->sum('amount') - $purchaseOrders->sum('total_amount'),
            ]
        ]);
    }

    public function vendorPerformance()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->with(['purchaseOrders' => function($q) use ($property) {
                $q->where('property_id', $property->id)
                  ->where('status', '!=', 'CANCELLED');
            }])
            ->get();

        $performance = $vendors->map(function($vendor) {
            $pos = $vendor->purchaseOrders;
            return [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'total_orders' => $pos->count(),
                'total_value' => $pos->sum('total_amount'),
                'completed_orders' => $pos->where('status', 'RECEIVED')->count(),
                'pending_orders' => $pos->whereIn('status', ['APPROVED', 'SENT'])->count(),
            ];
        })->sortByDesc('total_value')->values();

        return response()->json([
            'success' => true,
            'vendor_performance' => $performance
        ]);
    }

    public function inventoryValuation()
    {
        $user = Auth::user();
        $property = $user->property;

        $stockLevels = StockLevel::where('property_id', $property->id)
            ->with(['item.category', 'warehouse'])
            ->get();

        $byWarehouse = $stockLevels->groupBy('warehouse.name')->map(function($items, $warehouse) {
            return [
                'warehouse' => $warehouse,
                'total_items' => $items->count(),
                'total_quantity' => $items->sum('quantity'),
                'total_value' => $items->sum(function($stock) {
                    return $stock->quantity * ($stock->item->unit_cost ?? 0);
                }),
            ];
        });

        $byCategory = $stockLevels->groupBy('item.category.name')->map(function($items, $category) {
            return [
                'category' => $category,
                'total_items' => $items->count(),
                'total_quantity' => $items->sum('quantity'),
                'total_value' => $items->sum(function($stock) {
                    return $stock->quantity * ($stock->item->unit_cost ?? 0);
                }),
            ];
        });

        $totalValue = $stockLevels->sum(function($stock) {
            return $stock->quantity * ($stock->item->unit_cost ?? 0);
        });

        return response()->json([
            'success' => true,
            'valuation' => [
                'total_value' => $totalValue,
                'total_items' => $stockLevels->count(),
                'by_warehouse' => $byWarehouse->values(),
                'by_category' => $byCategory->values(),
            ]
        ]);
    }
}
