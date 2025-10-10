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
use App\Models\ReservationRoomRate;
use App\Models\ReservationStatusHistory;
use App\Models\Folio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check authorization
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access reservations.');
        }

        $query = Reservation::query()
            ->with(['guest', 'property', 'reservationRooms.roomType', 'creator']);

        // Filter by tenant's properties
        if ($user->role->name === 'DIRECTOR') {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        } elseif ($user->role->name === 'MANAGER' && $user->property_id) {
            $query->where('property_id', $user->property_id);
        } else {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('arrival_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('departure_date', '<=', $request->end_date);
        }

        // Search by guest name or confirmation number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('guest', function($gq) use ($search) {
                    $gq->where('full_name', 'ILIKE', "%{$search}%");
                })
                ->orWhere('external_reference', 'ILIKE', "%{$search}%");
            });
        }

        $reservations = $query->latest('created_at')->paginate(15);

        // Get properties for filter dropdown
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('Users.tenant.reservations.index', compact('reservations', 'properties'));
    }

    /**
     * Show the form for creating a new reservation
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create reservations.');
        }

        // Get user's properties
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        // Get guests for autocomplete
        $guests = Guest::where('tenant_id', $user->tenant_id)
            ->latest()
            ->limit(50)
            ->get();

        // If property is pre-selected, get room types
        $roomTypes = collect();
        if ($request->filled('property_id')) {
            $roomTypes = RoomType::where('property_id', $request->property_id)
                ->where('is_active', true)
                ->with('ratePlans')
                ->get();
        }

        return view('Users.tenant.reservations.create', compact('properties', 'guests', 'roomTypes'));
    }

    /**
     * Store a newly created reservation
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create reservations.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'guest_id' => 'required|uuid|exists:guests,id',
            'arrival_date' => 'required|date|after_or_equal:today',
            'departure_date' => 'required|date|after:arrival_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
            'source' => 'nullable|string|max:50',
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type_id' => 'required|uuid|exists:room_types,id',
            'rooms.*.rate_plan_id' => 'required|uuid|exists:rate_plans,id',
            'rooms.*.guest_name' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // Verify guest belongs to tenant
            $guest = Guest::where('id', $validated['guest_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // Check room availability
            $arrivalDate = Carbon::parse($validated['arrival_date']);
            $departureDate = Carbon::parse($validated['departure_date']);
            $nights = $arrivalDate->diffInDays($departureDate);

            $totalAmount = 0;
            $roomsData = [];

            foreach ($validated['rooms'] as $roomData) {
                $roomType = RoomType::findOrFail($roomData['room_type_id']);
                $ratePlan = RatePlan::findOrFail($roomData['rate_plan_id']);

                // Check if room is available
                $availableRooms = Room::where('room_type_id', $roomType->id)
                    ->where('property_id', $property->id)
                    ->where('status', 'AVAILABLE')
                    ->whereDoesntHave('reservationRooms', function($q) use ($arrivalDate, $departureDate) {
                        $q->whereHas('reservation', function($rq) use ($arrivalDate, $departureDate) {
                            $rq->where(function($dq) use ($arrivalDate, $departureDate) {
                                $dq->whereBetween('arrival_date', [$arrivalDate, $departureDate])
                                   ->orWhereBetween('departure_date', [$arrivalDate, $departureDate])
                                   ->orWhere(function($oq) use ($arrivalDate, $departureDate) {
                                       $oq->where('arrival_date', '<=', $arrivalDate)
                                          ->where('departure_date', '>=', $departureDate);
                                   });
                            })
                            ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN']);
                        });
                    })
                    ->first();

                if (!$availableRooms) {
                    throw new \Exception("No available rooms for {$roomType->name}");
                }

                $roomAmount = $ratePlan->rate * $nights;
                $totalAmount += $roomAmount;

                $roomsData[] = [
                    'room_id' => $availableRooms->id,
                    'room_type_id' => $roomType->id,
                    'rate_plan_id' => $ratePlan->id,
                    'guest_name' => $roomData['guest_name'] ?? $guest->full_name,
                    'amount' => $roomAmount,
                    'rate' => $ratePlan->rate,
                ];
            }

            // Create reservation
            $reservation = Reservation::create([
                'property_id' => $property->id,
                'guest_id' => $guest->id,
                'status' => 'PENDING',
                'arrival_date' => $arrivalDate,
                'departure_date' => $departureDate,
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'total_amount' => $totalAmount,
                'discount_amount' => 0,
                'special_requests' => $validated['special_requests'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'source' => $validated['source'] ?? 'FRONT_DESK',
                'created_by' => $user->id,
            ]);

            // Create reservation rooms
            foreach ($roomsData as $roomData) {
                $reservationRoom = ReservationRoom::create([
                    'reservation_id' => $reservation->id,
                    'room_id' => $roomData['room_id'],
                    'room_type_id' => $roomData['room_type_id'],
                    'rate_plan_id' => $roomData['rate_plan_id'],
                    'status' => 'RESERVED',
                    'guest_name' => $roomData['guest_name'],
                ]);

                // Create daily rates
                $currentDate = $arrivalDate->copy();
                while ($currentDate < $departureDate) {
                    ReservationRoomRate::create([
                        'reservation_room_id' => $reservationRoom->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'rate' => $roomData['rate'],
                        'updated_by' => $user->id,
                    ]);
                    $currentDate->addDay();
                }
            }

            // Create status history
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'old_status' => null,
                'new_status' => 'PENDING',
                'changed_by' => $user->id,
                'notes' => 'Reservation created',
            ]);

            // Create folio
            Folio::create([
                'reservation_id' => $reservation->id,
                'status' => 'OPEN',
                'balance' => $totalAmount,
                'currency' => $property->tenant->base_currency ?? 'USD',
                'created_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.reservations.show', $reservation)
                ->with('success', 'Reservation created successfully.');
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
        
        // Verify tenant ownership through property
        if ($reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this reservation.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to view reservations.');
        }

        $reservation->load([
            'guest',
            'property',
            'reservationRooms.room',
            'reservationRooms.roomType',
            'reservationRooms.ratePlan',
            'reservationRooms.roomRates',
            'statusHistory.changer',
            'folio.folioItems',
            'folio.payments',
            'creator',
            'updater'
        ]);

        return view('Users.tenant.reservations.show', compact('reservation'));
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($reservation->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this reservation.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return back()->with('error', 'You do not have permission to update reservations.');
        }

        $validated = $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,CHECKED_IN,CHECKED_OUT,CANCELLED,NO_SHOW',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $reservation->status;
            $newStatus = $validated['status'];

            // Update reservation status
            $reservation->update([
                'status' => $newStatus,
                'updated_by' => $user->id,
            ]);

            // Update room statuses based on reservation status
            if ($newStatus === 'CHECKED_IN') {
                $reservation->reservationRooms()->update([
                    'status' => 'OCCUPIED',
                    'check_in_time' => now(),
                ]);
                
                // Update room status to OCCUPIED
                foreach ($reservation->reservationRooms as $resRoom) {
                    $resRoom->room->update(['status' => 'OCCUPIED']);
                }
            } elseif ($newStatus === 'CHECKED_OUT') {
                $reservation->reservationRooms()->update([
                    'status' => 'COMPLETED',
                    'check_out_time' => now(),
                ]);
                
                // Update room status to DIRTY
                foreach ($reservation->reservationRooms as $resRoom) {
                    $resRoom->room->update(['status' => 'DIRTY']);
                }

                // Close folio
                if ($reservation->folio && $reservation->folio->balance <= 0) {
                    $reservation->folio->update(['status' => 'CLOSED']);
                }
            } elseif ($newStatus === 'CANCELLED') {
                $reservation->reservationRooms()->update(['status' => 'COMPLETED']);
                
                // Make rooms available again
                foreach ($reservation->reservationRooms as $resRoom) {
                    if ($resRoom->room->status === 'RESERVED') {
                        $resRoom->room->update(['status' => 'AVAILABLE']);
                    }
                }
            }

            // Create status history
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $user->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            return back()->with('success', 'Reservation status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update reservation status: ' . $e->getMessage());
        }
    }

    /**
     * Get available rooms for a date range
     */
    public function getAvailableRooms(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
        ]);

        $property = Property::where('id', $validated['property_id'])
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $arrivalDate = Carbon::parse($validated['arrival_date']);
        $departureDate = Carbon::parse($validated['departure_date']);

        $roomTypes = RoomType::where('property_id', $property->id)
            ->where('is_active', true)
            ->with(['ratePlans' => function($q) use ($arrivalDate, $departureDate) {
                $q->where(function($rq) use ($arrivalDate, $departureDate) {
                    $rq->whereNull('start_date')
                       ->orWhere(function($dq) use ($arrivalDate, $departureDate) {
                           $dq->where('start_date', '<=', $departureDate)
                              ->where(function($eq) use ($arrivalDate) {
                                  $eq->whereNull('end_date')
                                     ->orWhere('end_date', '>=', $arrivalDate);
                              });
                       });
                });
            }])
            ->get()
            ->map(function($roomType) use ($property, $arrivalDate, $departureDate) {
                // Count available rooms
                $availableCount = Room::where('room_type_id', $roomType->id)
                    ->where('property_id', $property->id)
                    ->where('status', 'AVAILABLE')
                    ->whereDoesntHave('reservationRooms', function($q) use ($arrivalDate, $departureDate) {
                        $q->whereHas('reservation', function($rq) use ($arrivalDate, $departureDate) {
                            $rq->where(function($dq) use ($arrivalDate, $departureDate) {
                                $dq->whereBetween('arrival_date', [$arrivalDate, $departureDate])
                                   ->orWhereBetween('departure_date', [$arrivalDate, $departureDate])
                                   ->orWhere(function($oq) use ($arrivalDate, $departureDate) {
                                       $oq->where('arrival_date', '<=', $arrivalDate)
                                          ->where('departure_date', '>=', $departureDate);
                                   });
                            })
                            ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN']);
                        });
                    })
                    ->count();

                $roomType->available_count = $availableCount;
                return $roomType;
            })
            ->filter(function($roomType) {
                return $roomType->available_count > 0;
            });

        return response()->json($roomTypes);
    }
}
