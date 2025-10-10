<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    /**
     * Display a listing of guests
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check authorization - DIRECTOR, MANAGER, RECEPTIONIST can manage guests
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access guest management.');
        }

        $query = Guest::where('tenant_id', $user->tenant_id)
            ->with(['creator', 'reservations']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('phone', 'ILIKE', "%{$search}%")
                  ->orWhere('id_number', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by nationality
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        $guests = $query->latest()->paginate(15);

        return view('Users.tenant.guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new guest
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create guests.');
        }

        return view('Users.tenant.guests.create');
    }

    /**
     * Store a newly created guest
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create guests.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('guests')->where(function ($query) use ($user) {
                    return $query->where('tenant_id', $user->tenant_id);
                })
            ],
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
                'marketing_consent' => $validated['marketing_consent'] ?? false,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.guests.show', $guest)
                ->with('success', 'Guest created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create guest: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified guest
     */
    public function show(Guest $guest)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to view guests.');
        }

        $guest->load(['reservations.property', 'reservations.reservationRooms.room', 'creator', 'updater']);

        return view('Users.tenant.guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified guest
     */
    public function edit(Guest $guest)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to edit guests.');
        }

        return view('Users.tenant.guests.edit', compact('guest'));
    }

    /**
     * Update the specified guest
     */
    public function update(Request $request, Guest $guest)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to edit guests.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('guests')->where(function ($query) use ($user) {
                    return $query->where('tenant_id', $user->tenant_id);
                })->ignore($guest->id)
            ],
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
                'marketing_consent' => $validated['marketing_consent'] ?? false,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.guests.show', $guest)
                ->with('success', 'Guest updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update guest: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified guest
     */
    public function destroy(Guest $guest)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        // Only DIRECTOR and MANAGER can delete guests
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return back()->with('error', 'You do not have permission to delete guests.');
        }

        // Check if guest has active reservations
        $activeReservations = $guest->reservations()
            ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN'])
            ->count();

        if ($activeReservations > 0) {
            return back()->with('error', 'Cannot delete guest with active reservations.');
        }

        try {
            $guest->delete();
            return redirect()->route('tenant.guests.index')
                ->with('success', 'Guest deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete guest: ' . $e->getMessage());
        }
    }

    /**
     * Search guests for AJAX requests
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'RECEPTIONIST'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $search = $request->get('q', '');
        
        $guests = Guest::where('tenant_id', $user->tenant_id)
            ->where(function($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('phone', 'ILIKE', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'full_name', 'email', 'phone']);

        return response()->json($guests);
    }
}
