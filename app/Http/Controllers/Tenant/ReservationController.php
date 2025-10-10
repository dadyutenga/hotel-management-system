<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\ReservationStatusHistory;
use App\Models\Room;
use App\Models\Folio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $query = Reservation::where('tenant_id', $tenant->id);

        if ($property) {
            $query->where('property_id', $property->id);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('check_in_from')) {
            $query->where('check_in_date', '>=', $request->check_in_from);
        }
        if ($request->filled('check_in_to')) {
            $query->where('check_in_date', '<=', $request->check_in_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('guest', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('confirmation_number', 'like', "%{$search}%");
        }

        $query->with(['guest', 'property', 'reservationRooms.room', 'reservationRooms.roomType']);

        $sortBy = $request->get('sort_by', 'check_in_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $reservations = $query->paginate(20);

        return response()->json([
            'success' => true,
            'reservations' => $reservations
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $reservation = Reservation::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with([
                'guest.contacts',
                'property',
                'reservationRooms.room.floor.building',
                'reservationRooms.roomType',
                'reservationRooms.ratePlan',
                'reservationRooms.roomRates',
                'statusHistory.changedBy',
                'folio.folioItems',
                'creator',
                'updater'
            ])
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'reservation' => $reservation
        ]);
    }

    public function checkIn(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $reservation = Reservation::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with('reservationRooms.room')
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        if ($reservation->status !== 'CONFIRMED') {
            return response()->json([
                'success' => false,
                'message' => 'Only confirmed reservations can be checked in'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'room_assignments' => 'required|array',
            'room_assignments.*.reservation_room_id' => 'required|uuid',
            'room_assignments.*.room_id' => 'required|uuid',
            'check_in_time' => 'nullable|date',
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

            $checkInTime = $request->check_in_time ? Carbon::parse($request->check_in_time) : now();

            foreach ($request->room_assignments as $assignment) {
                $reservationRoom = ReservationRoom::where('id', $assignment['reservation_room_id'])
                    ->where('reservation_id', $reservation->id)
                    ->first();

                if (!$reservationRoom) {
                    throw new \Exception("Reservation room not found: {$assignment['reservation_room_id']}");
                }

                $room = Room::where('tenant_id', $tenant->id)
                    ->where('id', $assignment['room_id'])
                    ->first();

                if (!$room) {
                    throw new \Exception("Room not found: {$assignment['room_id']}");
                }

                if ($room->status === 'OCCUPIED') {
                    throw new \Exception("Room {$room->room_number} is currently occupied");
                }

                $reservationRoom->update([
                    'room_id' => $room->id,
                    'status' => 'OCCUPIED',
                    'check_in_time' => $checkInTime,
                ]);

                $room->update(['status' => 'OCCUPIED']);
            }

            // Update reservation status
            $reservation->update([
                'status' => 'CHECKED_IN',
                'actual_check_in' => $checkInTime,
                'updated_by' => $user->id,
            ]);

            // Create status history
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'old_status' => 'CONFIRMED',
                'new_status' => 'CHECKED_IN',
                'changed_by' => $user->id,
                'notes' => $request->notes ?? 'Guest checked in',
            ]);

            if (!$reservation->folio) {
                Folio::create([
                    'tenant_id' => $tenant->id,
                    'reservation_id' => $reservation->id,
                    'guest_id' => $reservation->guest_id,
                    'status' => 'OPEN',
                    'balance' => 0,
                    'created_by' => $user->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Guest checked in successfully',
                'reservation' => $reservation->load(['reservationRooms.room', 'folio'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error checking in reservation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking in: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkOut(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $reservation = Reservation::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with(['reservationRooms.room', 'folio'])
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        if ($reservation->status !== 'CHECKED_IN') {
            return response()->json([
                'success' => false,
                'message' => 'Only checked-in reservations can be checked out'
            ], 400);
        }

        if ($reservation->folio && $reservation->folio->balance > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out with outstanding balance. Please settle the folio first.',
                'outstanding_balance' => $reservation->folio->balance
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'check_out_time' => 'nullable|date',
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

            $checkOutTime = $request->check_out_time ? Carbon::parse($request->check_out_time) : now();

            foreach ($reservation->reservationRooms as $reservationRoom) {
                $reservationRoom->update([
                    'status' => 'COMPLETED',
                    'check_out_time' => $checkOutTime,
                ]);

                if ($reservationRoom->room) {
                    $reservationRoom->room->update(['status' => 'DIRTY']);
                }
            }

            $reservation->update([
                'status' => 'CHECKED_OUT',
                'actual_check_out' => $checkOutTime,
                'updated_by' => $user->id,
            ]);

            // Create status history
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'old_status' => 'CHECKED_IN',
                'new_status' => 'CHECKED_OUT',
                'changed_by' => $user->id,
                'notes' => $request->notes ?? 'Guest checked out',
            ]);

            if ($reservation->folio) {
                $reservation->folio->update(['status' => 'CLOSED']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Guest checked out successfully',
                'reservation' => $reservation->load('reservationRooms.room')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error checking out reservation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking out: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableRooms(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $reservation = Reservation::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        $rooms = Room::where('tenant_id', $tenant->id)
            ->where('property_id', $reservation->property_id)
            ->whereIn('status', ['VACANT', 'CLEAN'])
            ->with(['floor.building', 'roomType', 'features'])
            ->orderBy('room_number')
            ->get();

        return response()->json([
            'success' => true,
            'rooms' => $rooms
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $reservation = Reservation::where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with('reservationRooms.room')
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        if (in_array($reservation->status, ['CHECKED_OUT', 'CANCELLED', 'NO_SHOW'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel reservation in current status'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $reservation->status;

            foreach ($reservation->reservationRooms as $reservationRoom) {
                if ($reservationRoom->room && $reservationRoom->status === 'OCCUPIED') {
                    $reservationRoom->room->update(['status' => 'VACANT']);
                }
                
                $reservationRoom->update(['status' => 'COMPLETED']);
            }

            $reservation->update([
                'status' => 'CANCELLED',
                'cancellation_reason' => $request->reason,
                'updated_by' => $user->id,
            ]);

            // Create status history
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'old_status' => $oldStatus,
                'new_status' => 'CANCELLED',
                'changed_by' => $user->id,
                'notes' => $request->reason,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully',
                'reservation' => $reservation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error cancelling reservation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling reservation: ' . $e->getMessage()
            ], 500);
        }
    }
}
