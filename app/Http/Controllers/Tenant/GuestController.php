<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\GuestContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Guest::with(['contacts', 'property'])
            ->where('tenant_id', $user->tenant_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $guests = $query->latest()->paginate(20);

        return view('Users.tenant.guests.index', compact('guests'));
    }

    public function create()
    {
        return view('Users.tenant.guests.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $guest = Guest::create([
                'tenant_id' => $user->tenant_id,
                'property_id' => $user->property_id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
                'id_type' => $validated['id_type'] ?? null,
                'id_number' => $validated['id_number'] ?? null,
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

    public function show(Guest $guest)
    {
        $user = Auth::user();

        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        $guest->load(['contacts', 'property', 'reservations.room']);

        return view('Users.tenant.guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        $user = Auth::user();

        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        return view('Users.tenant.guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        $user = Auth::user();

        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
        ]);

        try {
            $guest->update(array_merge($validated, [
                'updated_by' => $user->id
            ]));

            return redirect()->route('tenant.guests.show', $guest)
                ->with('success', 'Guest updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update guest: ' . $e->getMessage());
        }
    }

    public function destroy(Guest $guest)
    {
        $user = Auth::user();

        if ($guest->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this guest.');
        }

        try {
            $guest->delete();
            return redirect()->route('tenant.guests.index')
                ->with('success', 'Guest deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete guest: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('q', '');

        $guests = Guest::where('tenant_id', $user->tenant_id)
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function($guest) {
                return [
                    'id' => $guest->id,
                    'name' => $guest->first_name . ' ' . $guest->last_name,
                    'email' => $guest->email,
                    'phone' => $guest->phone,
                ];
            });

        return response()->json($guests);
    }
}
