<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties for the current tenant
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get properties for the current tenant
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->withCount(['buildings', 'rooms', 'users'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('Users.tenant.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new property
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only DIRECTOR role can create properties
        if ($user->role->name !== 'DIRECTOR') {
            return redirect()->route('tenant.properties.index')
                ->with('error', 'Only business directors can create new properties.');
        }

        // Get available timezones
        $timezones = [
            'Africa/Dar_es_Salaam' => 'East Africa Time (UTC+3)',
            'UTC' => 'UTC',
            'Africa/Nairobi' => 'East Africa Time - Nairobi',
            'Africa/Kampala' => 'East Africa Time - Kampala',
            'Africa/Kigali' => 'Central Africa Time - Kigali',
            'Africa/Bujumbura' => 'Central Africa Time - Bujumbura',
        ];

        return view('Users.tenant.properties.create', compact('timezones'));
    }

    /**
     * Store a newly created property in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only DIRECTOR role can create properties
        if ($user->role->name !== 'DIRECTOR') {
            return redirect()->route('tenant.properties.index')
                ->with('error', 'Only business directors can create new properties.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'contact_phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create the property - use user's tenant_id
            $property = Property::create([
                'tenant_id' => $user->tenant_id,
                'name' => $validated['name'],
                'address' => $validated['address'],
                'contact_phone' => $validated['contact_phone'],
                'email' => $validated['email'],
                'website' => $validated['website'],
                'timezone' => $validated['timezone'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('tenant.properties.show', $property->id)
                ->with('success', 'Property created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create property. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified property
     */
    public function show(Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to property.');
        }

        // Load relationships with counts
        $property->load([
            'buildings.floors.rooms',
            'users.role'
        ]);

        // Get property statistics
        $stats = [
            'total_buildings' => $property->buildings->count(),
            'total_floors' => $property->buildings->sum(fn($building) => $building->floors->count()),
            'total_rooms' => $property->rooms->count(),
            'active_users' => $property->users->where('is_active', true)->count(),
            'total_users' => $property->users->count(),
        ];

        // Recent activity (last 10 users who logged in)
        $recentActivity = $property->users()
            ->whereNotNull('last_login_at')
            ->with('role')
            ->orderBy('last_login_at', 'desc')
            ->take(10)
            ->get();

        return view('Users.tenant.properties.show', compact('property', 'stats', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified property
     */
    public function edit(Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to property.');
        }
        
        // Only DIRECTOR and MANAGER roles can edit properties
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return redirect()->route('tenant.properties.show', $property->id)
                ->with('error', 'You do not have permission to edit this property.');
        }

        // Get available timezones
        $timezones = [
            'Africa/Dar_es_Salaam' => 'East Africa Time (UTC+3)',
            'UTC' => 'UTC',
            'Africa/Nairobi' => 'East Africa Time - Nairobi',
            'Africa/Kampala' => 'East Africa Time - Kampala',
            'Africa/Kigali' => 'Central Africa Time - Kigali',
            'Africa/Bujumbura' => 'Central Africa Time - Bujumbura',
        ];

        return view('Users.tenant.properties.edit', compact('property', 'timezones'));
    }

    /**
     * Update the specified property in storage
     */
    public function update(Request $request, Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to property.');
        }
        
        // Only DIRECTOR and MANAGER roles can edit properties
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return redirect()->route('tenant.properties.show', $property->id)
                ->with('error', 'You do not have permission to edit this property.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'contact_phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $property->update([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'contact_phone' => $validated['contact_phone'],
                'email' => $validated['email'],
                'website' => $validated['website'],
                'timezone' => $validated['timezone'],
                'is_active' => $request->boolean('is_active', $property->is_active),
            ]);

            DB::commit();

            return redirect()->route('tenant.properties.show', $property->id)
                ->with('success', 'Property updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update property. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified property from storage (soft delete)
     */
    public function destroy(Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to property.');
        }
        
        // Only DIRECTOR role can delete properties
        if ($user->role->name !== 'DIRECTOR') {
            return redirect()->route('tenant.properties.show', $property->id)
                ->with('error', 'Only business directors can delete properties.');
        }

        try {
            DB::beginTransaction();

            // Check if property has active users
            $activeUsers = $property->users()->where('is_active', true)->count();
            if ($activeUsers > 0) {
                return redirect()->route('tenant.properties.show', $property->id)
                    ->with('error', 'Cannot delete property with active users. Please deactivate all users first.');
            }

            // Check if property has active reservations (skip this for now if no reservations table)
            // $activeReservations = $property->reservations()
            //     ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN'])
            //     ->count();
            // 
            // if ($activeReservations > 0) {
            //     return redirect()->route('tenant.properties.show', $property->id)
            //         ->with('error', 'Cannot delete property with active reservations.');
            // }

            // Soft delete the property
            $property->delete();

            DB::commit();

            return redirect()->route('tenant.properties.index')
                ->with('success', 'Property deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tenant.properties.show', $property->id)
                ->with('error', 'Failed to delete property. Please try again.');
        }
    }

    /**
     * Toggle property active status
     */
    public function toggleStatus(Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to property.'
            ], 403);
        }
        
        // Only DIRECTOR and MANAGER roles can toggle status
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to change property status.'
            ], 403);
        }

        try {
            $property->update(['is_active' => !$property->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Property status updated successfully!',
                'is_active' => $property->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property status.'
            ], 500);
        }
    }

    /**
     * Get property statistics for dashboard
     */
    public function getStats(Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to property.');
        }

        $stats = [
            'buildings' => $property->buildings()->count(),
            'floors' => $property->buildings()->withCount('floors')->get()->sum('floors_count'),
            'rooms' => $property->rooms()->count(),
            'occupied_rooms' => 0, // Skip room status for now
            'available_rooms' => 0, // Skip room status for now  
            'maintenance_rooms' => 0, // Skip room status for now
            'total_users' => $property->users()->count(),
            'active_users' => $property->users()->where('is_active', true)->count(),
            'pending_reservations' => 0, // Skip reservations for now
            'confirmed_reservations' => 0, // Skip reservations for now
            'checked_in_guests' => 0, // Skip reservations for now
        ];

        return response()->json($stats);
    }

    /**
     * Assign user to property
     */
    public function assignUser(Request $request, Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to property.'
            ], 403);
        }
        
        // Only DIRECTOR and MANAGER roles can assign users
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to assign users to properties.'
            ], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        try {
            // Get user within current tenant context
            $targetUser = User::where('id', $validated['user_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            $targetUser->update(['property_id' => $property->id]);

            return response()->json([
                'success' => true,
                'message' => 'User assigned to property successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign user to property.'
            ], 500);
        }
    }

    /**
     * Remove user from property
     */
    public function removeUser(Request $request, Property $property)
    {
        $user = Auth::user();
        
        // Ensure property belongs to the authenticated tenant
        if ($property->tenant_id !== $user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to property.'
            ], 403);
        }
        
        // Only DIRECTOR and MANAGER roles can remove users
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to remove users from properties.'
            ], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        try {
            $targetUser = User::where('id', $validated['user_id'])
                ->where('tenant_id', $user->tenant_id)
                ->where('property_id', $property->id)
                ->firstOrFail();

            $targetUser->update(['property_id' => null]);

            return response()->json([
                'success' => true,
                'message' => 'User removed from property successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove user from property.'
            ], 500);
        }
    }
}