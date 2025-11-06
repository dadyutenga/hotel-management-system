<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\GuestContact;
use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuestController extends Controller
{
    /**
     * Display a listing of guests
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permissions - RECEPTIONIST, MANAGER can view guests
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access guests.');
        }

        $query = Guest::with(['creator', 'reservations'])
            ->where('tenant_id', $user->tenant_id);

        // Include soft deleted if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Filter by property for non-DIRECTOR users
        if ($user->role->name !== 'DIRECTOR' && $user->property_id) {
            $query->whereHas('reservations', function($q) use ($user) {
                $q->where('property_id', $user->property_id);
            });
        }

        // Search functionality
        if ($request->filled('query') || $request->filled('search')) {
            $search = $request->query ?? $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        // Filter by nationality
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $guests = $query->orderBy('created_at', 'desc')
                       ->paginate(15);

        // Get unique nationalities for filter
        $nationalities = Guest::where('tenant_id', $user->tenant_id)
            ->whereNotNull('nationality')
            ->distinct()
            ->pluck('nationality')
            ->sort()
            ->values();

        // Get statistics
        $stats = [
            'total' => Guest::where('tenant_id', $user->tenant_id)->count(),
            'with_reservations' => Guest::where('tenant_id', $user->tenant_id)
                ->whereHas('reservations')->count(),
            'checked_in' => Guest::where('tenant_id', $user->tenant_id)
                ->whereHas('reservations', function($q) {
                    $q->where('status', 'CHECKED_IN');
                })->count(),
        ];

        return view('Users.tenant.guests.index', compact('guests', 'stats', 'nationalities'));
    }

    /**
     * Show the form for creating a new guest
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only RECEPTIONIST can create guests
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.guests.index')
                ->with('error', 'You do not have permission to create guests.');
        }

        // Get unique nationalities for datalist
        $nationalities = Guest::where('tenant_id', $user->tenant_id)
            ->whereNotNull('nationality')
            ->distinct()
            ->pluck('nationality')
            ->sort()
            ->values();

        return view('Users.tenant.guests.create', compact('nationalities'));
    }

    /**
     * Store a newly created guest in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only RECEPTIONIST can create guests
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.guests.index')
                ->with('error', 'You do not have permission to create guests.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'nationality' => 'nullable|string|max:50',
            'id_type' => 'nullable|in:PASSPORT,NATIONAL_ID,DRIVING_LICENSE,OTHER',
            'id_number' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'marketing_consent' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $guest = Guest::create([
                'tenant_id' => $user->tenant_id,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'id_type' => $validated['id_type'] ?? null,
                'id_number' => $validated['id_number'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'marketing_consent' => $request->boolean('marketing_consent', false),
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.guests.show', $guest->id)
                ->with('success', 'Guest registered successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to register guest: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified guest
     */
    public function show(Guest $guest)
    {
        $user = Auth::user();

        // Check tenant isolation
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $guest->load(['creator', 'updater', 'contacts', 'reservations.creator', 'reservations.property']);

        // Get reservation stats
        $stats = [
            'reservations_count' => $guest->reservations->count(),
            'nights' => $guest->reservations->sum(function($reservation) {
                if ($reservation->arrival_date && $reservation->departure_date) {
                    return $reservation->arrival_date->diffInDays($reservation->departure_date);
                }
                return 0;
            }),
            'lifetime_value' => $guest->reservations->sum('total_amount'),
        ];

        // Get paginated reservations for the table
        $reservations = $guest->reservations()
            ->with(['creator', 'property', 'reservationRooms.room.roomType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('Users.tenant.guests.show', compact('guest', 'stats', 'reservations'));
    }

    /**
     * Show the form for editing the specified guest
     */
    public function edit(Guest $guest)
    {
        $user = Auth::user();

        // Check permissions
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.guests.index')
                ->with('error', 'You do not have permission to edit guests.');
        }

        // Check tenant isolation
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Get unique nationalities for datalist
        $nationalities = Guest::where('tenant_id', $user->tenant_id)
            ->whereNotNull('nationality')
            ->distinct()
            ->pluck('nationality')
            ->sort()
            ->values();

        return view('Users.tenant.guests.edit', compact('guest', 'nationalities'));
    }

    /**
     * Update the specified guest in storage
     */
    public function update(Request $request, Guest $guest)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!in_array($user->role->name, ['RECEPTIONIST', 'MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.guests.index')
                ->with('error', 'You do not have permission to edit guests.');
        }

        // Check tenant isolation
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'nationality' => 'nullable|string|max:50',
            'id_type' => 'nullable|in:PASSPORT,NATIONAL_ID,DRIVING_LICENSE,OTHER',
            'id_number' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'marketing_consent' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $guest->update([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'id_type' => $validated['id_type'] ?? null,
                'id_number' => $validated['id_number'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'marketing_consent' => $request->boolean('marketing_consent', false),
                'notes' => $validated['notes'] ?? null,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.guests.show', $guest->id)
                ->with('success', 'Guest updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update guest: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified guest from storage (Only MANAGER) or restore if soft deleted
     */
    public function destroy(Request $request, Guest $guest)
    {
        $user = Auth::user();

        // Only MANAGER and DIRECTOR can delete guests
        if (!in_array($user->role->name, ['MANAGER', 'DIRECTOR'])) {
            return redirect()->route('tenant.guests.index')
                ->with('error', 'You do not have permission to delete guests.');
        }

        // Check tenant isolation
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            DB::beginTransaction();

            // Handle restore if requested
            if ($request->boolean('restore') && $guest->trashed()) {
                $guest->restore();
                DB::commit();
                return redirect()->route('tenant.guests.index')
                    ->with('success', 'Guest restored successfully!');
            }

            // Check if guest has active reservations
            $hasActiveReservations = $guest->reservations()
                ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN'])
                ->exists();

            if ($hasActiveReservations) {
                return back()->with('error', 'Cannot archive guest with active reservations.');
            }

            $guest->delete();

            DB::commit();

            return redirect()->route('tenant.guests.index')
                ->with('success', 'Guest archived successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process request: ' . $e->getMessage());
        }
    }

    /**
     * Search for guests (AJAX)
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        if (!$request->filled('q')) {
            return response()->json([]);
        }

        $search = $request->q;

        $guests = Guest::where('tenant_id', $user->tenant_id)
            ->where(function($query) use ($search) {
                $query->where('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('id_number', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'full_name', 'email', 'phone']);

        return response()->json($guests);
    }
}