<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function stockLevels(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $query = StockLevel::where('property_id', $property->id);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('low_stock')) {
            $query->whereHas('item', function($q) {
                $q->whereRaw('stock_levels.quantity <= items.reorder_level');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $query->with(['item.category', 'warehouse']);

        $stockLevels = $query->paginate(50);

        return response()->json([
            'success' => true,
            'stock_levels' => $stockLevels
        ]);
    }

    public function lowStockAlert()
    {
        $user = Auth::user();
        $property = $user->property;

        $lowStock = StockLevel::where('property_id', $property->id)
            ->whereHas('item', function($q) {
                $q->whereRaw('stock_levels.quantity <= items.reorder_level');
            })
            ->with(['item.category', 'warehouse'])
            ->get();

        return response()->json([
            'success' => true,
            'low_stock_items' => $lowStock,
            'count' => $lowStock->count()
        ]);
    }

    public function stockMovements(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $query = StockMovement::where('property_id', $property->id);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }

        $query->with(['item', 'warehouse', 'creator']);
        $query->orderBy('movement_date', 'desc');

        $movements = $query->paginate(50);

        return response()->json([
            'success' => true,
            'movements' => $movements
        ]);
    }

    public function adjustStock(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|uuid|exists:items,id',
            'warehouse_id' => 'required|uuid|exists:warehouses,id',
            'quantity' => 'required|numeric',
            'adjustment_type' => 'required|in:ADD,SUBTRACT,SET',
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $stockLevel = StockLevel::where('property_id', $property->id)
                ->where('warehouse_id', $request->warehouse_id)
                ->where('item_id', $request->item_id)
                ->first();

            if (!$stockLevel) {
                $stockLevel = StockLevel::create([
                    'property_id' => $property->id,
                    'warehouse_id' => $request->warehouse_id,
                    'item_id' => $request->item_id,
                    'quantity' => 0,
                ]);
            }

            $oldQuantity = $stockLevel->quantity;
            $newQuantity = $oldQuantity;
            $movementQuantity = $request->quantity;

            switch ($request->adjustment_type) {
                case 'ADD':
                    $newQuantity = $oldQuantity + $request->quantity;
                    break;
                case 'SUBTRACT':
                    $newQuantity = $oldQuantity - $request->quantity;
                    $movementQuantity = -$request->quantity;
                    break;
                case 'SET':
                    $newQuantity = $request->quantity;
                    $movementQuantity = $newQuantity - $oldQuantity;
                    break;
            }

            if ($newQuantity < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock cannot be negative'
                ], 400);
            }

            $stockLevel->update(['quantity' => $newQuantity]);

            StockMovement::create([
                'property_id' => $property->id,
                'item_id' => $request->item_id,
                'warehouse_id' => $request->warehouse_id,
                'movement_type' => 'ADJUSTMENT',
                'quantity' => $movementQuantity,
                'unit_cost' => 0,
                'reference_type' => 'MANUAL_ADJUSTMENT',
                'reference_id' => null,
                'notes' => $request->reason,
                'movement_date' => now(),
                'created_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'stock_level' => $stockLevel->load(['item', 'warehouse'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adjusting stock: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error adjusting stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createPurchaseOrder(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|uuid|exists:vendors,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after_or_equal:order_date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|uuid|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $purchaseOrder = PurchaseOrder::create([
                'property_id' => $property->id,
                'vendor_id' => $request->vendor_id,
                'po_number' => $poNumber,
                'status' => 'DRAFT',
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'total_amount' => 0,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
                'created_by' => $user->id,
            ]);

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $quantity = $itemData['quantity'];
                $unitPrice = $itemData['unit_price'];
                $discountPercent = $itemData['discount_percent'] ?? 0;
                $taxPercent = $itemData['tax_percent'] ?? 0;

                $subtotal = $quantity * $unitPrice;
                $discount = $subtotal * ($discountPercent / 100);
                $afterDiscount = $subtotal - $discount;
                $tax = $afterDiscount * ($taxPercent / 100);
                $lineTotal = $afterDiscount + $tax;

                PurchaseOrderItem::create([
                    'po_id' => $purchaseOrder->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'tax_percent' => $taxPercent,
                    'line_total' => $lineTotal,
                    'received_quantity' => 0,
                ]);

                $totalAmount += $lineTotal;
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order created successfully',
                'purchase_order' => $purchaseOrder->load(['vendor', 'purchaseOrderItems.item'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating purchase order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPurchaseOrders(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $query = PurchaseOrder::where('property_id', $property->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('date_from')) {
            $query->where('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('order_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('po_number', 'like', "%{$request->search}%");
        }

        $query->with(['vendor', 'creator', 'approver']);
        $query->orderBy('order_date', 'desc');

        $purchaseOrders = $query->paginate(20);

        return response()->json([
            'success' => true,
            'purchase_orders' => $purchaseOrders
        ]);
    }

    public function showPurchaseOrder($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $purchaseOrder = PurchaseOrder::where('id', $id)
            ->where('property_id', $property->id)
            ->with([
                'vendor',
                'purchaseOrderItems.item.category',
                'creator',
                'approver',
                'goodsReceipts'
            ])
            ->first();

        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'purchase_order' => $purchaseOrder
        ]);
    }

    public function updatePurchaseOrder(Request $request, $id)
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

        if (!in_array($purchaseOrder->status, ['DRAFT'])) {
            return response()->json([
                'success' => false,
                'message' => 'Can only update draft purchase orders'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'sometimes|required|uuid|exists:vendors,id',
            'expected_delivery_date' => 'sometimes|required|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $purchaseOrder->update($request->only([
                'vendor_id',
                'expected_delivery_date',
                'delivery_address',
                'notes'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Purchase order updated successfully',
                'purchase_order' => $purchaseOrder
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating purchase order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receiveGoods(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'po_id' => 'required|uuid|exists:purchase_orders,id',
            'warehouse_id' => 'required|uuid|exists:warehouses,id',
            'receipt_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.po_item_id' => 'required|uuid|exists:purchase_order_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
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

            $purchaseOrder = PurchaseOrder::where('id', $request->po_id)
                ->where('property_id', $property->id)
                ->first();

            if (!$purchaseOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not found'
                ], 404);
            }

            $receiptNumber = 'GR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $goodsReceipt = GoodsReceipt::create([
                'po_id' => $purchaseOrder->id,
                'receipt_number' => $receiptNumber,
                'receipt_date' => $request->receipt_date,
                'warehouse_id' => $request->warehouse_id,
                'notes' => $request->notes,
                'created_by' => $user->id,
            ]);

            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::find($itemData['po_item_id']);
                
                if ($poItem && $poItem->po_id == $purchaseOrder->id) {
                    GoodsReceiptItem::create([
                        'goods_receipt_id' => $goodsReceipt->id,
                        'po_item_id' => $poItem->id,
                        'item_id' => $poItem->item_id,
                        'quantity' => $itemData['quantity'],
                        'unit_cost' => $poItem->unit_price,
                    ]);

                    $poItem->increment('received_quantity', $itemData['quantity']);

                    $stockLevel = StockLevel::where('property_id', $property->id)
                        ->where('warehouse_id', $request->warehouse_id)
                        ->where('item_id', $poItem->item_id)
                        ->first();

                    if ($stockLevel) {
                        $stockLevel->increment('quantity', $itemData['quantity']);
                    } else {
                        StockLevel::create([
                            'property_id' => $property->id,
                            'warehouse_id' => $request->warehouse_id,
                            'item_id' => $poItem->item_id,
                            'quantity' => $itemData['quantity'],
                        ]);
                    }

                    StockMovement::create([
                        'property_id' => $property->id,
                        'item_id' => $poItem->item_id,
                        'warehouse_id' => $request->warehouse_id,
                        'movement_type' => 'INCOMING',
                        'quantity' => $itemData['quantity'],
                        'unit_cost' => $poItem->unit_price,
                        'reference_type' => 'GOODS_RECEIPT',
                        'reference_id' => $goodsReceipt->id,
                        'notes' => 'Received from PO: ' . $purchaseOrder->po_number,
                        'movement_date' => $request->receipt_date,
                        'created_by' => $user->id,
                    ]);
                }
            }

            $allReceived = $purchaseOrder->purchaseOrderItems()
                ->whereColumn('received_quantity', '>=', 'quantity')
                ->count() === $purchaseOrder->purchaseOrderItems()->count();

            if ($allReceived) {
                $purchaseOrder->update(['status' => 'RECEIVED']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Goods received successfully',
                'goods_receipt' => $goodsReceipt->load(['goodsReceiptItems.item', 'warehouse'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error receiving goods: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error receiving goods: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVendors(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $query = Vendor::where('tenant_id', $tenant->id);

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $vendors = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'vendors' => $vendors
        ]);
    }

    public function createVendor(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vendor = Vendor::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'tax_id' => $request->tax_id,
                'payment_terms' => $request->payment_terms,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully',
                'vendor' => $vendor
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating vendor: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating vendor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getWarehouses()
    {
        $user = Auth::user();
        $property = $user->property;

        $warehouses = Warehouse::where('property_id', $property->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'warehouses' => $warehouses
        ]);
    }

    public function inventoryReport(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $stockLevels = StockLevel::where('property_id', $property->id)
            ->with(['item.category', 'warehouse'])
            ->get();

        $totalValue = $stockLevels->sum(function($stock) {
            return $stock->quantity * ($stock->item->unit_cost ?? 0);
        });

        $lowStockCount = $stockLevels->filter(function($stock) {
            return $stock->quantity <= ($stock->item->reorder_level ?? 0);
        })->count();

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

        return response()->json([
            'success' => true,
            'report' => [
                'total_items' => $stockLevels->count(),
                'total_value' => $totalValue,
                'low_stock_count' => $lowStockCount,
                'by_category' => $byCategory->values(),
            ]
        ]);
    }
}
