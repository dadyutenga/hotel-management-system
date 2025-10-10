<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\PosOrder;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Authorization - DIRECTOR, MANAGER, ACCOUNTANT can access reports
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access reports.');
        }

        return view('Users.tenant.reports.index');
    }

    /**
     * Occupancy Report
     */
    public function occupancy(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access reports.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Get properties based on role
        $propertiesQuery = Property::where('tenant_id', $user->tenant_id);
        if ($user->role->name === 'MANAGER' && $user->property_id) {
            $propertiesQuery->where('id', $user->property_id);
        }
        $properties = $propertiesQuery->get();

        $occupancyData = [];

        foreach ($properties as $property) {
            // Total rooms in property
            $totalRooms = Room::where('property_id', $property->id)->count();
            
            if ($totalRooms === 0) {
                continue;
            }

            // Calculate occupied room nights
            $occupiedNights = Reservation::where('property_id', $property->id)
                ->whereIn('status', ['CHECKED_IN', 'CHECKED_OUT'])
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('arrival_date', [$startDate, $endDate])
                      ->orWhereBetween('departure_date', [$startDate, $endDate])
                      ->orWhere(function($oq) use ($startDate, $endDate) {
                          $oq->where('arrival_date', '<=', $startDate)
                             ->where('departure_date', '>=', $endDate);
                      });
                })
                ->get()
                ->sum(function($reservation) use ($startDate, $endDate) {
                    $checkIn = Carbon::parse($reservation->arrival_date)->max($startDate);
                    $checkOut = Carbon::parse($reservation->departure_date)->min($endDate);
                    return $checkIn->diffInDays($checkOut);
                });

            $totalNights = $startDate->diffInDays($endDate) * $totalRooms;
            $occupancyRate = $totalNights > 0 ? ($occupiedNights / $totalNights) * 100 : 0;

            $occupancyData[] = [
                'property' => $property,
                'total_rooms' => $totalRooms,
                'occupied_nights' => $occupiedNights,
                'total_nights' => $totalNights,
                'occupancy_rate' => round($occupancyRate, 2),
            ];
        }

        return view('Users.tenant.reports.occupancy', compact('occupancyData', 'startDate', 'endDate'));
    }

    /**
     * Revenue Report
     */
    public function revenue(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access reports.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Get properties based on role
        $propertiesQuery = Property::where('tenant_id', $user->tenant_id);
        if ($user->role->name === 'MANAGER' && $user->property_id) {
            $propertiesQuery->where('id', $user->property_id);
        }
        $properties = $propertiesQuery->get();

        $revenueData = [];

        foreach ($properties as $property) {
            // Room revenue
            $roomRevenue = Payment::whereHas('folio.reservation', function($q) use ($property, $startDate, $endDate) {
                $q->where('property_id', $property->id)
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', 'COMPLETED')
            ->sum('amount');

            // F&B revenue (POS)
            $fbRevenue = PosOrder::whereHas('outlet.property', function($q) use ($property) {
                $q->where('id', $property->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'COMPLETED')
            ->sum('total_amount');

            $totalRevenue = $roomRevenue + $fbRevenue;

            // Number of reservations
            $reservationCount = Reservation::where('property_id', $property->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT'])
                ->count();

            // Average daily rate (ADR)
            $totalRoomNights = Reservation::where('property_id', $property->id)
                ->whereBetween('arrival_date', [$startDate, $endDate])
                ->whereIn('status', ['CHECKED_IN', 'CHECKED_OUT'])
                ->get()
                ->sum(function($reservation) {
                    return Carbon::parse($reservation->arrival_date)
                        ->diffInDays(Carbon::parse($reservation->departure_date));
                });

            $adr = $totalRoomNights > 0 ? $roomRevenue / $totalRoomNights : 0;

            $revenueData[] = [
                'property' => $property,
                'room_revenue' => $roomRevenue,
                'fb_revenue' => $fbRevenue,
                'total_revenue' => $totalRevenue,
                'reservation_count' => $reservationCount,
                'adr' => round($adr, 2),
            ];
        }

        return view('Users.tenant.reports.revenue', compact('revenueData', 'startDate', 'endDate'));
    }

    /**
     * Guest Report
     */
    public function guests(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access reports.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Total guests
        $totalGuests = Guest::where('tenant_id', $user->tenant_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Guests by nationality
        $guestsByNationality = Guest::where('tenant_id', $user->tenant_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('nationality')
            ->select('nationality', DB::raw('count(*) as count'))
            ->groupBy('nationality')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Repeat guests (guests with more than one reservation)
        $repeatGuests = Guest::where('tenant_id', $user->tenant_id)
            ->whereHas('reservations', function($q) {
                $q->whereIn('status', ['CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT']);
            }, '>', 1)
            ->count();

        // Marketing consent
        $marketingConsent = Guest::where('tenant_id', $user->tenant_id)
            ->where('marketing_consent', true)
            ->count();

        return view('Users.tenant.reports.guests', compact(
            'totalGuests',
            'guestsByNationality',
            'repeatGuests',
            'marketingConsent',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Reservation Report
     */
    public function reservations(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'ACCOUNTANT'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access reports.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Query base
        $query = Reservation::whereHas('property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        });

        if ($user->role->name === 'MANAGER' && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        $query->whereBetween('created_at', [$startDate, $endDate]);

        // Reservations by status
        $reservationsByStatus = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Reservations by source
        $reservationsBySource = (clone $query)
            ->whereNotNull('source')
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get();

        // Average length of stay
        $avgLengthOfStay = (clone $query)
            ->whereIn('status', ['CHECKED_IN', 'CHECKED_OUT'])
            ->get()
            ->avg(function($reservation) {
                return Carbon::parse($reservation->arrival_date)
                    ->diffInDays(Carbon::parse($reservation->departure_date));
            });

        // Cancellation rate
        $totalReservations = (clone $query)->count();
        $cancelledReservations = (clone $query)->where('status', 'CANCELLED')->count();
        $cancellationRate = $totalReservations > 0 ? ($cancelledReservations / $totalReservations) * 100 : 0;

        // No-show rate
        $noShowReservations = (clone $query)->where('status', 'NO_SHOW')->count();
        $noShowRate = $totalReservations > 0 ? ($noShowReservations / $totalReservations) * 100 : 0;

        return view('Users.tenant.reports.reservations', compact(
            'reservationsByStatus',
            'reservationsBySource',
            'avgLengthOfStay',
            'cancellationRate',
            'noShowRate',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Housekeeping Report
     */
    public function housekeeping(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this report.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        // Query base
        $query = \App\Models\HousekeepingTask::whereHas('property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        });

        if ($user->role->name === 'MANAGER' && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        $query->whereBetween('scheduled_date', [$startDate, $endDate]);

        // Tasks by status
        $tasksByStatus = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Tasks by priority
        $tasksByPriority = (clone $query)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        // Average completion time (in hours)
        $avgCompletionTime = (clone $query)
            ->where('status', 'COMPLETED')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get()
            ->avg(function($task) {
                return Carbon::parse($task->started_at)
                    ->diffInHours(Carbon::parse($task->completed_at));
            });

        // Tasks per housekeeper
        $tasksPerHousekeeper = (clone $query)
            ->with('assignedTo')
            ->get()
            ->groupBy('assigned_to')
            ->map(function($tasks, $userId) {
                return [
                    'housekeeper' => $tasks->first()->assignedTo,
                    'total_tasks' => $tasks->count(),
                    'completed_tasks' => $tasks->where('status', 'COMPLETED')->count(),
                ];
            });

        return view('Users.tenant.reports.housekeeping', compact(
            'tasksByStatus',
            'tasksByPriority',
            'avgCompletionTime',
            'tasksPerHousekeeper',
            'startDate',
            'endDate'
        ));
    }
}
