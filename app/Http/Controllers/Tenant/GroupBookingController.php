<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\GroupBooking;
use App\Models\Guest;
use App\Models\Property;
use App\Models\CorporateAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GroupBookingController extends Controller
{
    /**
     * Display a listing of group bookings
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permissions - RECEPTIONIST, MANAGER, DIRECTOR can view group bookings
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access group bookings.');
        }

        $query = GroupBooking::with(['property', 'leaderGuest', 'corporateAccount', 'creator', 'reservations'])
            ->whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });

        // Include soft deleted if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Filter by property for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id) {
            $query->where('property_id', $user->property_id);
        }

        // Search functionality
        if ($request->filled('query') || $request->filled('search')) {
            $search = $request->query ?? $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('leaderGuest', function($gq) use ($search) {
                      $gq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('corporateAccount', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by date range
        if ($request->filled('arrival_date')) {
            $query->where('arrival_date', '>=', $request->arrival_date);
        }
        if ($request->filled('departure_date')) {
            $query->where('departure_date', '<=', $request->departure_date);
        }

        $groupBookings = $query->orderBy('created_at', 'desc')
                               ->paginate(15);

        // Get properties for filter
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get statistics
        $stats = [
            'total' => GroupBooking::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->count(),
            'pending' => GroupBooking::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->where('status', 'PENDING')->count(),
            'confirmed' => GroupBooking::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->where('status', 'CONFIRMED')->count(),
            'total_rooms' => GroupBooking::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })->whereIn('status', ['PENDING', 'CONFIRMED'])->sum('total_rooms'),
        ];

        // Route to role-specific views
        if ($user->role->name === 'RECEPTIONIST') {
            return view('Users.tenant.group-bookings.Receptionist.index', compact('groupBookings', 'stats', 'properties'));
        } else {
            // MANAGER and DIRECTOR use the same view
            return view('Users.tenant.group-bookings.Manager.index', compact('groupBookings', 'stats', 'properties'));
        }
    }

    /**
     * Show the form for creating a new group booking
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only RECEPTIONIST, MANAGER, DIRECTOR can create group bookings
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.group-bookings.index')
                ->with('error', 'You do not have permission to create group bookings.');
        }

        // Get properties based on user role
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $properties = Property::where('tenant_id', $user->tenant_id)
                ->where('id', $user->property_id)
                ->where('is_active', true)
                ->get(['id', 'name']);
        }

        // Get corporate accounts for the tenant
        $corporateAccounts = CorporateAccount::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Route to role-specific views
        if ($user->role->name === 'RECEPTIONIST') {
            return view('Users.tenant.group-bookings.Receptionist.create', compact('properties', 'corporateAccounts'));
        } else {
            return view('Users.tenant.group-bookings.Manager.create', compact('properties', 'corporateAccounts'));
        }
    }

    /**
     * Store a newly created group booking in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only RECEPTIONIST, MANAGER, DIRECTOR can create group bookings
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.group-bookings.index')
                ->with('error', 'You do not have permission to create group bookings.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'name' => 'required|string|max:255',
            'leader_guest_id' => 'nullable|uuid|exists:res.guests,id',
            'corporate_account_id' => 'nullable|uuid|exists:res.corporate_accounts,id',
            'total_rooms' => 'required|integer|min:1',
            'arrival_date' => 'required|date|after_or_equal:today',
            'departure_date' => 'required|date|after:arrival_date',
            'status' => 'required|in:PENDING,CONFIRMED,CANCELLED,COMPLETED',
            'notes' => 'nullable|string',
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

            // If leader guest is provided, verify it belongs to tenant
            if ($validated['leader_guest_id']) {
                $guest = Guest::where('id', $validated['leader_guest_id'])
                    ->where('tenant_id', $user->tenant_id)
                    ->firstOrFail();
            }

            // If corporate account is provided, verify it belongs to tenant
            if ($validated['corporate_account_id']) {
                $corporateAccount = CorporateAccount::where('id', $validated['corporate_account_id'])
                    ->where('tenant_id', $user->tenant_id)
                    ->firstOrFail();
            }

            $groupBooking = GroupBooking::create([
                'property_id' => $validated['property_id'],
                'name' => $validated['name'],
                'leader_guest_id' => $validated['leader_guest_id'] ?? null,
                'corporate_account_id' => $validated['corporate_account_id'] ?? null,
                'total_rooms' => $validated['total_rooms'],
                'arrival_date' => $validated['arrival_date'],
                'departure_date' => $validated['departure_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.group-bookings.show', $groupBooking->id)
                ->with('success', 'Group booking created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create group booking: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified group booking
     */
    public function show(GroupBooking $groupBooking)
    {
        $user = Auth::user();

        // Check tenant isolation via property
        if ($groupBooking->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $groupBooking->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        $groupBooking->load([
            'property',
            'leaderGuest',
            'corporateAccount',
            'creator',
            'updater',
            'reservations.guest',
            'reservations.reservationRooms.room',
            'reservations.reservationRooms.roomType'
        ]);

        // Get group booking stats
        $stats = [
            'reservations_count' => $groupBooking->reservations->count(),
            'total_guests' => $groupBooking->reservations->sum(function($reservation) {
                return $reservation->adults + $reservation->children;
            }),
            'total_amount' => $groupBooking->reservations->sum('total_amount'),
            'nights' => $groupBooking->arrival_date->diffInDays($groupBooking->departure_date),
            'rooms_assigned' => $groupBooking->reservations->sum(function($reservation) {
                return $reservation->reservationRooms->count();
            }),
        ];

        // Get paginated reservations for the table
        $reservations = $groupBooking->reservations()
            ->with(['guest', 'creator', 'reservationRooms.room', 'reservationRooms.roomType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Route to role-specific views
        if ($user->role->name === 'RECEPTIONIST') {
            return view('Users.tenant.group-bookings.Receptionist.show', compact('groupBooking', 'stats', 'reservations'));
        } else {
            return view('Users.tenant.group-bookings.Manager.show', compact('groupBooking', 'stats', 'reservations'));
        }
    }

    /**
     * Show the form for editing the specified group booking
     */
    public function edit(GroupBooking $groupBooking)
    {
        $user = Auth::user();

        // Check permissions
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.group-bookings.index')
                ->with('error', 'You do not have permission to edit group bookings.');
        }

        // Check tenant isolation via property
        if ($groupBooking->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $groupBooking->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        // Get properties based on user role
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $properties = Property::where('tenant_id', $user->tenant_id)
                ->where('id', $user->property_id)
                ->where('is_active', true)
                ->get(['id', 'name']);
        }

        // Get corporate accounts for the tenant
        $corporateAccounts = CorporateAccount::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Route to role-specific views
        if ($user->role->name === 'RECEPTIONIST') {
            return view('Users.tenant.group-bookings.Receptionist.edit', compact('groupBooking', 'properties', 'corporateAccounts'));
        } else {
            return view('Users.tenant.group-bookings.Manager.edit', compact('groupBooking', 'properties', 'corporateAccounts'));
        }
    }

    /**
     * Update the specified group booking in storage
     */
    public function update(Request $request, GroupBooking $groupBooking)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.group-bookings.index')
                ->with('error', 'You do not have permission to edit group bookings.');
        }

        // Check tenant isolation via property
        if ($groupBooking->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $groupBooking->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'name' => 'required|string|max:255',
            'leader_guest_id' => 'nullable|uuid|exists:res.guests,id',
            'corporate_account_id' => 'nullable|uuid|exists:res.corporate_accounts,id',
            'total_rooms' => 'required|integer|min:1',
            'arrival_date' => 'required|date',
            'departure_date' => 'required|date|after:arrival_date',
            'status' => 'required|in:PENDING,CONFIRMED,CANCELLED,COMPLETED',
            'notes' => 'nullable|string',
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

            // If leader guest is provided, verify it belongs to tenant
            if ($validated['leader_guest_id']) {
                $guest = Guest::where('id', $validated['leader_guest_id'])
                    ->where('tenant_id', $user->tenant_id)
                    ->firstOrFail();
            }

            // If corporate account is provided, verify it belongs to tenant
            if ($validated['corporate_account_id']) {
                $corporateAccount = CorporateAccount::where('id', $validated['corporate_account_id'])
                    ->where('tenant_id', $user->tenant_id)
                    ->firstOrFail();
            }

            $groupBooking->update([
                'property_id' => $validated['property_id'],
                'name' => $validated['name'],
                'leader_guest_id' => $validated['leader_guest_id'] ?? null,
                'corporate_account_id' => $validated['corporate_account_id'] ?? null,
                'total_rooms' => $validated['total_rooms'],
                'arrival_date' => $validated['arrival_date'],
                'departure_date' => $validated['departure_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.group-bookings.show', $groupBooking->id)
                ->with('success', 'Group booking updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update group booking: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified group booking from storage (Only MANAGER and DIRECTOR) or restore if soft deleted
     */
    public function destroy(Request $request, GroupBooking $groupBooking)
    {
        $user = Auth::user();

        // Only MANAGER and DIRECTOR can delete group bookings
        if (!in_array($user->role->name, ['MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.group-bookings.index')
                ->with('error', 'You do not have permission to delete group bookings.');
        }

        // Check tenant isolation via property
        if ($groupBooking->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check property access for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id !== $groupBooking->property_id) {
            abort(403, 'You do not have access to this property.');
        }

        try {
            DB::beginTransaction();

            // Handle restore if requested
            if ($request->boolean('restore') && $groupBooking->trashed()) {
                $groupBooking->restore();
                DB::commit();
                return redirect()->route('tenant.group-bookings.index')
                    ->with('success', 'Group booking restored successfully!');
            }

            // Check if group booking has active reservations
            $hasActiveReservations = $groupBooking->reservations()
                ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN'])
                ->exists();

            if ($hasActiveReservations) {
                return back()->with('error', 'Cannot archive group booking with active reservations.');
            }

            $groupBooking->delete();

            DB::commit();

            return redirect()->route('tenant.group-bookings.index')
                ->with('success', 'Group booking archived successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process request: ' . $e->getMessage());
        }
    }

    /**
     * Search for group bookings (AJAX)
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        if (!$request->filled('q')) {
            return response()->json([]);
        }

        $search = $request->q;

        $groupBookings = GroupBooking::whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('leaderGuest', function($gq) use ($search) {
                          $gq->where('full_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('corporateAccount', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      });
            })
            ->with(['property:id,name', 'leaderGuest:id,full_name'])
            ->limit(10)
            ->get(['id', 'name', 'property_id', 'leader_guest_id', 'status', 'arrival_date', 'departure_date', 'total_rooms']);

        return response()->json($groupBookings);
    }
}
