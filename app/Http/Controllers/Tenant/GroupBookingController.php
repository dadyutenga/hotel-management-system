<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\GroupBooking;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GroupBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $query = GroupBooking::where('property_id', $property->id ?? null);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('arrival_from')) {
            $query->where('arrival_date', '>=', $request->arrival_from);
        }

        if ($request->filled('arrival_to')) {
            $query->where('arrival_date', '<=', $request->arrival_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('leaderGuest', function($gq) use ($search) {
                      $gq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $query->with(['leaderGuest', 'corporateAccount', 'property', 'creator']);

        $sortBy = $request->get('sort_by', 'arrival_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $groupBookings = $query->paginate(20);

        return response()->json([
            'success' => true,
            'group_bookings' => $groupBookings
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'leader_guest_id' => 'required|uuid|exists:guests,id',
            'corporate_account_id' => 'nullable|uuid|exists:corporate_accounts,id',
            'total_rooms' => 'required|integer|min:1',
            'arrival_date' => 'required|date|after_or_equal:today',
            'departure_date' => 'required|date|after:arrival_date',
            'notes' => 'nullable|string',
            'reservations' => 'nullable|array',
            'reservations.*.guest_id' => 'required|uuid|exists:guests,id',
            'reservations.*.room_type_id' => 'required|uuid|exists:room_types,id',
            'reservations.*.number_of_rooms' => 'required|integer|min:1',
            'reservations.*.adults' => 'required|integer|min:1',
            'reservations.*.children' => 'nullable|integer|min:0',
            'reservations.*.special_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $leaderGuest = Guest::where('id', $request->leader_guest_id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$leaderGuest) {
            return response()->json([
                'success' => false,
                'message' => 'Leader guest not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $groupBooking = GroupBooking::create([
                'property_id' => $property->id,
                'name' => $request->name,
                'leader_guest_id' => $leaderGuest->id,
                'corporate_account_id' => $request->corporate_account_id,
                'total_rooms' => $request->total_rooms,
                'arrival_date' => $request->arrival_date,
                'departure_date' => $request->departure_date,
                'status' => 'PENDING',
                'notes' => $request->notes,
                'created_by' => $user->id,
            ]);

            if ($request->filled('reservations')) {
                foreach ($request->reservations as $resData) {
                    $guest = Guest::where('id', $resData['guest_id'])
                        ->where('tenant_id', $tenant->id)
                        ->first();

                    if ($guest) {
                        $roomType = RoomType::where('id', $resData['room_type_id'])
                            ->where('property_id', $property->id)
                            ->first();

                        if ($roomType) {
                            $reservation = Reservation::create([
                                'tenant_id' => $tenant->id,
                                'property_id' => $property->id,
                                'guest_id' => $guest->id,
                                'group_booking_id' => $groupBooking->id,
                                'check_in_date' => $request->arrival_date,
                                'check_out_date' => $request->departure_date,
                                'adults' => $resData['adults'],
                                'children' => $resData['children'] ?? 0,
                                'number_of_rooms' => $resData['number_of_rooms'],
                                'status' => 'CONFIRMED',
                                'confirmation_number' => 'GRP-' . strtoupper(substr(uniqid(), -8)),
                                'special_requests' => $resData['special_requests'] ?? null,
                                'source' => 'GROUP_BOOKING',
                                'created_by' => $user->id,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group booking created successfully',
                'group_booking' => $groupBooking->load(['leaderGuest', 'reservations'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating group booking: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating group booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $id)
            ->where('property_id', $property->id)
            ->with([
                'leaderGuest.contacts',
                'corporateAccount',
                'property',
                'reservations.guest',
                'reservations.reservationRooms.room',
                'reservations.reservationRooms.roomType',
                'creator',
                'updater'
            ])
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'group_booking' => $groupBooking
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'leader_guest_id' => 'sometimes|required|uuid|exists:guests,id',
            'corporate_account_id' => 'nullable|uuid|exists:corporate_accounts,id',
            'total_rooms' => 'sometimes|required|integer|min:1',
            'arrival_date' => 'sometimes|required|date',
            'departure_date' => 'sometimes|required|date|after:arrival_date',
            'status' => 'sometimes|required|in:PENDING,CONFIRMED,CANCELLED,COMPLETED',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'updated_by' => $user->id,
            ];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }
            if ($request->has('leader_guest_id')) {
                $updateData['leader_guest_id'] = $request->leader_guest_id;
            }
            if ($request->has('corporate_account_id')) {
                $updateData['corporate_account_id'] = $request->corporate_account_id;
            }
            if ($request->has('total_rooms')) {
                $updateData['total_rooms'] = $request->total_rooms;
            }
            if ($request->has('arrival_date')) {
                $updateData['arrival_date'] = $request->arrival_date;
            }
            if ($request->has('departure_date')) {
                $updateData['departure_date'] = $request->departure_date;
            }
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }
            if ($request->has('notes')) {
                $updateData['notes'] = $request->notes;
            }

            $groupBooking->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Group booking updated successfully',
                'group_booking' => $groupBooking->load(['leaderGuest', 'reservations'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating group booking: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating group booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirm($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $id)
            ->where('property_id', $property->id)
            ->with('reservations')
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        if ($groupBooking->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending group bookings can be confirmed'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $groupBooking->update([
                'status' => 'CONFIRMED',
                'updated_by' => $user->id,
            ]);

            foreach ($groupBooking->reservations as $reservation) {
                if ($reservation->status === 'PENDING') {
                    $reservation->update(['status' => 'CONFIRMED']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group booking confirmed successfully',
                'group_booking' => $groupBooking
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error confirming group booking: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error confirming group booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $id)
            ->where('property_id', $property->id)
            ->with('reservations')
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        if (in_array($groupBooking->status, ['CANCELLED', 'COMPLETED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel group booking in current status'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $groupBooking->update([
                'status' => 'CANCELLED',
                'notes' => $groupBooking->notes . "\n\nCancellation Reason: " . $request->cancellation_reason,
                'updated_by' => $user->id,
            ]);

            foreach ($groupBooking->reservations as $reservation) {
                if (!in_array($reservation->status, ['CHECKED_OUT', 'CANCELLED'])) {
                    $reservation->update([
                        'status' => 'CANCELLED',
                        'cancellation_reason' => 'Group booking cancelled: ' . $request->cancellation_reason,
                        'updated_by' => $user->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group booking cancelled successfully',
                'group_booking' => $groupBooking
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error cancelling group booking: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling group booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addReservation(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        if ($groupBooking->status === 'CANCELLED') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add reservations to cancelled group booking'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'guest_id' => 'required|uuid|exists:guests,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'number_of_rooms' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $guest = Guest::where('id', $request->guest_id)
                ->where('tenant_id', $tenant->id)
                ->first();

            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guest not found'
                ], 404);
            }

            $reservation = Reservation::create([
                'tenant_id' => $tenant->id,
                'property_id' => $property->id,
                'guest_id' => $guest->id,
                'group_booking_id' => $groupBooking->id,
                'check_in_date' => $groupBooking->arrival_date,
                'check_out_date' => $groupBooking->departure_date,
                'adults' => $request->adults,
                'children' => $request->children ?? 0,
                'number_of_rooms' => $request->number_of_rooms,
                'status' => $groupBooking->status === 'CONFIRMED' ? 'CONFIRMED' : 'PENDING',
                'confirmation_number' => 'GRP-' . strtoupper(substr(uniqid(), -8)),
                'special_requests' => $request->special_requests,
                'source' => 'GROUP_BOOKING',
                'created_by' => $user->id,
            ]);

            $groupBooking->increment('total_rooms', $request->number_of_rooms);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation added to group booking successfully',
                'reservation' => $reservation->load('guest')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adding reservation to group: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeReservation($groupId, $reservationId)
    {
        $user = Auth::user();
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $groupId)
            ->where('property_id', $property->id)
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        $reservation = Reservation::where('id', $reservationId)
            ->where('group_booking_id', $groupBooking->id)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found in this group booking'
            ], 404);
        }

        if (in_array($reservation->status, ['CHECKED_IN', 'CHECKED_OUT'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove checked-in or checked-out reservations'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $roomCount = $reservation->number_of_rooms;

            $reservation->update([
                'group_booking_id' => null,
                'status' => 'CANCELLED',
                'cancellation_reason' => 'Removed from group booking',
                'updated_by' => $user->id,
            ]);

            $groupBooking->decrement('total_rooms', $roomCount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation removed from group booking successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error removing reservation from group: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error removing reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableRooms($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        $availableRooms = Room::where('property_id', $property->id)
            ->whereIn('status', ['VACANT', 'CLEAN'])
            ->with(['floor.building', 'roomType', 'features'])
            ->orderBy('room_number')
            ->get();

        $roomTypes = RoomType::where('property_id', $property->id)
            ->withCount(['rooms' => function($query) {
                $query->whereIn('status', ['VACANT', 'CLEAN']);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'available_rooms' => $availableRooms,
            'room_types' => $roomTypes
        ]);
    }

    public function getSummary($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $groupBooking = GroupBooking::where('id', $id)
            ->where('property_id', $property->id)
            ->with('reservations')
            ->first();

        if (!$groupBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Group booking not found'
            ], 404);
        }

        $reservations = $groupBooking->reservations;

        $summary = [
            'total_reservations' => $reservations->count(),
            'total_rooms' => $reservations->sum('number_of_rooms'),
            'total_adults' => $reservations->sum('adults'),
            'total_children' => $reservations->sum('children'),
            'confirmed_reservations' => $reservations->where('status', 'CONFIRMED')->count(),
            'pending_reservations' => $reservations->where('status', 'PENDING')->count(),
            'checked_in_reservations' => $reservations->where('status', 'CHECKED_IN')->count(),
            'checked_out_reservations' => $reservations->where('status', 'CHECKED_OUT')->count(),
            'nights' => Carbon::parse($groupBooking->arrival_date)->diffInDays($groupBooking->departure_date),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }
}
