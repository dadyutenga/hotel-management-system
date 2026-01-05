<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RatePlan;
use App\Models\ReservationRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permissions - RECEPTIONIST, MANAGER, DIRECTOR can view reservations
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access reservations.');
        }

        $query = Reservation::with(['property', 'guest', 'creator', 'reservationRooms.room', 'reservationRooms.roomType']);

        // Filter by tenant via property
        if ($user->role->name === 'DIRECTOR') {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        } else {
            // Filter by user's property for RECEPTIONIST and MANAGER
            $query->where('property_id', $user->property_id);
        }

        // Search by guest
        if ($request->filled('guest')) {
            $query->whereHas('guest', function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->guest}%")
                  ->orWhere('email', 'like', "%{$request->guest}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->where('arrival_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('departure_date', '<=', $request->to);
        }

        // Filter by property
        if ($request->filled('property')) {
            $query->where('property_id', $request->property);
        }

        $reservations = $query->orderBy('arrival_date', 'desc')
                              ->paginate(15);

        // Get properties for filter
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $properties = Property::where('id', $user->property_id)
                ->where('is_active', true)
                ->get(['id', 'name']);
        }

        // Calculate metrics
        $today = now()->toDateString();
        
        $metrics = [
            'arrivals_today' => Reservation::whereHas('property', function($q) use ($user) {
                    if ($user->role->name === 'DIRECTOR') {
                        $q->where('tenant_id', $user->tenant_id);
                    } else {
                        $q->where('id', $user->property_id);
                    }
                })
                ->where('arrival_date', $today)
                ->whereIn('status', ['CONFIRMED', 'PENDING'])
                ->count(),
            
            'departures_today' => Reservation::whereHas('property', function($q) use ($user) {
                    if ($user->role->name === 'DIRECTOR') {
                        $q->where('tenant_id', $user->tenant_id);
                    } else {
                        $q->where('id', $user->property_id);
                    }
                })
                ->where('departure_date', $today)
                ->where('status', 'CHECKED_IN')
                ->count(),
            
            'in_house' => Reservation::whereHas('property', function($q) use ($user) {
                    if ($user->role->name === 'DIRECTOR') {
                        $q->where('tenant_id', $user->tenant_id);
                    } else {
                        $q->where('id', $user->property_id);
                    }
                })
                ->where('status', 'CHECKED_IN')
                ->count(),
            
            'occupancy_rate' => '0%', // Calculate based on total rooms vs occupied
        ];

        // Calculate occupancy rate
        if ($user->role->name === 'DIRECTOR') {
            $totalRooms = Room::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->count();
        } else {
            $totalRooms = Room::where('property_id', $user->property_id)->count();
        }

        if ($totalRooms > 0) {
            $occupancyRate = round(($metrics['in_house'] / $totalRooms) * 100, 1);
            $metrics['occupancy_rate'] = $occupancyRate . '%';
        }

        return view('Users.tenant.reservations.index', compact('reservations', 'properties', 'metrics'));
    }

    /**
     * Show the form for creating a new reservation
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only RECEPTIONIST, MANAGER, DIRECTOR can create reservations
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.reservations.index')
                ->with('error', 'You do not have permission to create reservations.');
        }

        // Get properties based on user role
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $properties = Property::where('id', $user->property_id)
                ->where('is_active', true)
                ->get(['id', 'name']);
        }

        // Get room types for the user's accessible properties
        if ($user->role->name === 'DIRECTOR') {
            $roomTypes = RoomType::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->with('property')->orderBy('name')->get();
        } else {
            $roomTypes = RoomType::where('property_id', $user->property_id)
                ->with('property')
                ->orderBy('name')
                ->get();
        }

        return view('Users.tenant.reservations.create', compact('properties', 'roomTypes'));
    }

    /**
     * Store a newly created reservation in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only RECEPTIONIST, MANAGER, DIRECTOR can create reservations
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.reservations.index')
                ->with('error', 'You do not have permission to create reservations.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'guest_id' => 'required|uuid|exists:guests,id',
            'group_booking_id' => 'nullable|uuid',
            'status' => 'required|in:PENDING,CONFIRMED,HOLD',
            'arrival_date' => 'required|date|after_or_equal:today',
            'departure_date' => 'required|date|after:arrival_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
            'source' => 'nullable|string|max:50',
            'external_reference' => 'nullable|string|max:255',
            'room_type_ids' => 'required|array|min:1',
            'room_type_ids.*' => 'required|uuid|exists:room_types,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // For non-DIRECTOR users, verify property assignment
            if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $property->id) {
                throw ValidationException::withMessages([
                    'property_id' => 'You do not have access to this property.'
                ]);
            }

            // Verify guest belongs to tenant
            $guest = Guest::where('id', $validated['guest_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // Create the reservation
            $reservation = Reservation::create([
                'property_id' => $validated['property_id'],
                'guest_id' => $validated['guest_id'],
                'group_booking_id' => null,
                'corporate_account_id' => null,
                'status' => $validated['status'],
                'arrival_date' => $validated['arrival_date'],
                'departure_date' => $validated['departure_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'discount_reason' => $validated['discount_reason'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'source' => $validated['source'] ?? 'FRONT_DESK',
                'external_reference' => $validated['external_reference'] ?? null,
                'created_by' => $user->id,
            ]);

            // Create reservation rooms (without specific room assignment yet)
            $defaultRatePlan = RatePlan::where('property_id', $validated['property_id'])->first();
            
            foreach ($validated['room_type_ids'] as $roomTypeId) {
                // Verify room type belongs to the property
                $roomType = RoomType::where('id', $roomTypeId)
                    ->where('property_id', $validated['property_id'])
                    ->firstOrFail();

                ReservationRoom::create([
                    'reservation_id' => $reservation->id,
                    'room_id' => null, // Will be assigned later
                    'room_type_id' => $roomTypeId,
                    'rate_plan_id' => $defaultRatePlan?->id,
                    'status' => 'RESERVED',
                ]);
            }

            DB::commit();

            return redirect()->route('tenant.reservations.show', $reservation->id)
                ->with('success', 'Reservation created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create reservation: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified reservation
     */
    public function show(Reservation $reservation)
    {
        $user = Auth::user();

        // Check tenant isolation via property
        if ($reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $reservation->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        $reservation->load([
            'property',
            'guest',
            'creator',
            'updater',
            'reservationRooms.room',
            'reservationRooms.roomType',
            'reservationRooms.ratePlan',
            'folio.folioItems',
            'statusHistory.user'
        ]);

        // Calculate nights
        $nights = $reservation->arrival_date->diffInDays($reservation->departure_date);

        // Get folio balance
        $balance = 0;
        if ($reservation->folio) {
            $charges = $reservation->folio->folioItems()->where('type', 'CHARGE')->sum('amount');
            $payments = $reservation->folio->folioItems()->where('type', 'PAYMENT')->sum('amount');
            $balance = $charges - $payments;
        }

        return view('Users.tenant.reservations.show', compact('reservation', 'nights', 'balance'));
    }

    /**
     * Show the form for editing the specified reservation
     */
    public function edit(Reservation $reservation)
    {
        $user = Auth::user();

        // Check permissions
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.reservations.index')
                ->with('error', 'You do not have permission to edit reservations.');
        }

        // Check tenant isolation via property
        if ($reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $reservation->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        // Get properties based on user role
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $properties = Property::where('id', $user->property_id)
                ->where('is_active', true)
                ->get(['id', 'name']);
        }

        // Get room types
        if ($user->role->name === 'DIRECTOR') {
            $roomTypes = RoomType::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->with('property')->orderBy('name')->get();
        } else {
            $roomTypes = RoomType::where('property_id', $user->property_id)
                ->with('property')
                ->orderBy('name')
                ->get();
        }

        $reservation->load(['reservationRooms.room', 'reservationRooms.roomType']);

        return view('Users.tenant.reservations.edit', compact('reservation', 'properties', 'roomTypes'));
    }

    /**
     * Update the specified reservation in storage
     */
    public function update(Request $request, Reservation $reservation)
    {
        $user = Auth::user();

        // Check permissions
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.reservations.index')
                ->with('error', 'You do not have permission to edit reservations.');
        }

        // Check tenant isolation via property
        if ($reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $reservation->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'guest_id' => 'required|uuid|exists:guests,id',
            'group_booking_id' => 'nullable|uuid',
            'status' => 'required|in:PENDING,CONFIRMED,CHECKED_IN,CHECKED_OUT,CANCELLED,NO_SHOW,HOLD',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
            'source' => 'nullable|string|max:50',
            'external_reference' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // For non-DIRECTOR users, verify property assignment
            if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $property->id) {
                throw ValidationException::withMessages([
                    'property_id' => 'You do not have access to this property.'
                ]);
            }

            // Verify guest belongs to tenant
            $guest = Guest::where('id', $validated['guest_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            $reservation->update([
                'property_id' => $validated['property_id'],
                'guest_id' => $validated['guest_id'],
                'group_booking_id' => null,
                'corporate_account_id' => null,
                'status' => $validated['status'],
                'arrival_date' => $validated['arrival_date'],
                'departure_date' => $validated['departure_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'discount_reason' => $validated['discount_reason'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'source' => $validated['source'] ?? $reservation->source,
                'external_reference' => $validated['external_reference'] ?? null,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.reservations.show', $reservation->id)
                ->with('success', 'Reservation updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update reservation: ' . $e->getMessage());
        }
    }

    /**
     * Update the reservation status
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $user = Auth::user();

        // Check permissions
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.reservations.index')
                ->with('error', 'You do not have permission to update reservations.');
        }

        // Check tenant isolation via property
        if ($reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $reservation->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        $validated = $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,CHECKED_IN,CHECKED_OUT,CANCELLED,NO_SHOW,HOLD',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $reservation->status;
            
            $reservation->update([
                'status' => $validated['status'],
                'updated_by' => $user->id,
            ]);

            // Log status change (if you have ReservationStatusHistory model)
            // ReservationStatusHistory::create([...]);

            DB::commit();

            return back()->with('success', "Reservation status updated from {$oldStatus} to {$validated['status']}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Get available rooms for booking
     */
    public function getAvailableRooms(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
        ]);

        // Verify property access
        $property = Property::where('id', $validated['property_id'])
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $property->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get rooms that are NOT occupied during the requested period
        $occupiedRoomIds = ReservationRoom::whereHas('reservation', function($q) use ($validated) {
            $q->where('property_id', $validated['property_id'])
              ->where(function($query) use ($validated) {
                  $query->whereBetween('arrival_date', [$validated['arrival_date'], $validated['departure_date']])
                        ->orWhereBetween('departure_date', [$validated['arrival_date'], $validated['departure_date']])
                        ->orWhere(function($q) use ($validated) {
                            $q->where('arrival_date', '<=', $validated['arrival_date'])
                              ->where('departure_date', '>=', $validated['departure_date']);
                        });
              })
              ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN']);
        })->whereNotNull('room_id')->pluck('room_id');

        $availableRooms = Room::where('property_id', $validated['property_id'])
            ->where('room_type_id', $validated['room_type_id'])
            ->where('status', 'CLEAN')
            ->whereNotIn('id', $occupiedRoomIds)
            ->with('roomType', 'floor')
            ->get();

        return response()->json($availableRooms);
    }

    /**
     * Remove the specified reservation from storage (Only MANAGER and DIRECTOR) or restore if soft deleted
     */
    public function destroy(Request $request, Reservation $reservation)
    {
        $user = Auth::user();

        // Only MANAGER and DIRECTOR can delete reservations
        if (!in_array($user->role->name, ['MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.reservations.index')
                ->with('error', 'You do not have permission to delete reservations.');
        }

        // Check tenant isolation via property
        if ($reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $reservation->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        try {
            DB::beginTransaction();

            // Handle restore if requested
            if ($request->boolean('restore') && $reservation->trashed()) {
                $reservation->restore();
                DB::commit();
                return redirect()->route('tenant.reservations.index')
                    ->with('success', 'Reservation restored successfully!');
            }

            // Check if reservation is checked in
            if ($reservation->status === 'CHECKED_IN') {
                return back()->with('error', 'Cannot delete a checked-in reservation. Please check out the guest first.');
            }

            // Check if there's a folio with charges
            if ($reservation->folio) {
                $hasCharges = $reservation->folio->folioItems()->where('type', 'CHARGE')->exists();
                if ($hasCharges) {
                    return back()->with('error', 'Cannot delete reservation with folio charges. Please settle the folio first.');
                }
            }

            $reservation->delete();

            DB::commit();

            return redirect()->route('tenant.reservations.index')
                ->with('success', 'Reservation cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process request: ' . $e->getMessage());
        }
    }

    /**
     * Search for reservations (AJAX)
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        if (!$request->filled('q')) {
            return response()->json([]);
        }

        $search = $request->q;

        $query = Reservation::with(['property:id,name', 'guest:id,full_name,email'])
            ->whereHas('property', function($q) use ($user) {
                if ($user->role->name === 'DIRECTOR') {
                    $q->where('tenant_id', $user->tenant_id);
                } else {
                    $q->where('id', $user->property_id);
                }
            })
            ->where(function($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('external_reference', 'like', "%{$search}%")
                      ->orWhereHas('guest', function($gq) use ($search) {
                          $gq->where('full_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
                      });
            })
            ->limit(10)
            ->get(['id', 'property_id', 'guest_id', 'status', 'arrival_date', 'departure_date', 'total_amount', 'external_reference']);

        return response()->json($query);
    }
}
