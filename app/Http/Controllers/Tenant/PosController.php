<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosPayment;
use App\Models\Outlet;
use App\Models\MenuItem;
use App\Models\Room;
use App\Models\Folio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Display a listing of POS orders
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Authorization - BAR_TENDER, MANAGER, DIRECTOR can access
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'BAR_TENDER'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access POS.');
        }

        $query = PosOrder::with(['outlet', 'server', 'room', 'posOrderItems']);

        // Filter by tenant's outlets
        $query->whereHas('outlet.property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        });

        // Filter by outlet
        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            // Default: today's orders
            $query->whereDate('created_at', now());
        }

        $orders = $query->latest('created_at')->paginate(20);

        // Get outlets for filter
        $outlets = Outlet::whereHas('property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        })->get();

        return view('Users.tenant.pos.index', compact('orders', 'outlets'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'BAR_TENDER'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create POS orders.');
        }

        // Get outlets
        $outlets = Outlet::whereHas('property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        })->where('is_active', true)->get();

        // Get menu items if outlet is selected
        $menuItems = collect();
        if ($request->filled('outlet_id')) {
            $outlet = Outlet::find($request->outlet_id);
            if ($outlet && $outlet->menu_id) {
                $menuItems = MenuItem::where('menu_id', $outlet->menu_id)
                    ->where('is_available', true)
                    ->with('category')
                    ->get();
            }
        }

        return view('Users.tenant.pos.create', compact('outlets', 'menuItems'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'BAR_TENDER'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create POS orders.');
        }

        $validated = $request->validate([
            'outlet_id' => 'required|uuid|exists:outlets,id',
            'order_type' => 'required|in:DINE_IN,TAKE_AWAY,ROOM_SERVICE',
            'guest_count' => 'nullable|integer|min:1',
            'table_number' => 'nullable|string|max:20',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|uuid|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Verify outlet belongs to tenant
            $outlet = Outlet::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->findOrFail($validated['outlet_id']);

            // Generate order number
            $orderNumber = $this->generateOrderNumber($outlet->id);

            // Calculate totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $quantity = $itemData['quantity'];
                $itemTotal = $menuItem->price * $quantity;
                $subtotal += $itemTotal;

                $itemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $menuItem->price,
                    'line_total' => $itemTotal,
                    'special_instructions' => $itemData['special_instructions'] ?? null,
                ];
            }

            $taxAmount = $subtotal * 0.18; // 18% tax - should be configurable
            $serviceCharge = $subtotal * 0.10; // 10% service charge - should be configurable
            $discountAmount = $validated['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount + $serviceCharge - $discountAmount;

            // Get folio if room service
            $folioId = null;
            if ($validated['order_type'] === 'ROOM_SERVICE' && isset($validated['room_id'])) {
                $room = Room::find($validated['room_id']);
                // Find active reservation for this room
                $reservation = $room->reservationRooms()
                    ->whereHas('reservation', function($q) {
                        $q->where('status', 'CHECKED_IN');
                    })
                    ->first();
                
                if ($reservation && $reservation->reservation->folio) {
                    $folioId = $reservation->reservation->folio->id;
                }
            }

            // Create order
            $order = PosOrder::create([
                'outlet_id' => $outlet->id,
                'folio_id' => $folioId,
                'room_id' => $validated['room_id'] ?? null,
                'order_number' => $orderNumber,
                'server_id' => $user->id,
                'status' => 'OPEN',
                'order_type' => $validated['order_type'],
                'guest_count' => $validated['guest_count'] ?? 1,
                'table_number' => $validated['table_number'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_charge' => $serviceCharge,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items
            foreach ($itemsData as $itemData) {
                PosOrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $itemData['menu_item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $itemData['line_total'],
                    'special_instructions' => $itemData['special_instructions'],
                    'status' => 'PENDING',
                ]);
            }

            // If room service with folio, add charge to folio
            if ($folioId) {
                $folio = Folio::find($folioId);
                \App\Models\FolioItem::create([
                    'folio_id' => $folio->id,
                    'description' => "Room Service - Order #{$orderNumber}",
                    'amount' => $totalAmount,
                    'type' => 'F&B',
                    'reference_id' => $order->id,
                    'reference_type' => 'PosOrder',
                    'created_by' => $user->id,
                ]);
            }

            DB::commit();

            return redirect()->route('tenant.pos.show', $order)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order
     */
    public function show(PosOrder $pos)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($pos->outlet->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $pos->load(['outlet', 'server', 'room', 'folio.reservation', 'posOrderItems.menuItem', 'posPayments']);

        return view('Users.tenant.pos.show', compact('pos'));
    }

    /**
     * Process payment for order
     */
    public function processPayment(Request $request, PosOrder $pos)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($pos->outlet->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'BAR_TENDER'])) {
            return back()->with('error', 'You do not have permission to process payments.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:CASH,CARD,MOBILE,ROOM_CHARGE',
            'amount' => 'required|numeric|min:0.01',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Check if order is already completed
            if ($pos->status === 'COMPLETED') {
                return back()->with('error', 'Order is already completed.');
            }

            PosPayment::create([
                'order_id' => $pos->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'created_by' => $user->id,
            ]);

            // Check if order is fully paid
            $totalPaid = $pos->posPayments()->sum('amount');
            if ($totalPaid >= $pos->total_amount) {
                $pos->update([
                    'status' => 'COMPLETED',
                    'completed_at' => now(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber($outletId)
    {
        $today = date('Ymd');
        $lastOrder = PosOrder::where('outlet_id', $outletId)
            ->where('order_number', 'LIKE', "ORD-{$today}%")
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('ORD-%s-%04d', $today, $newNumber);
    }
}
