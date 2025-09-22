<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of users for the current tenant's properties
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get users for the current tenant
        $query = User::where('tenant_id', $user->tenant_id)
                    ->with(['role', 'property']);

        // If user is not DIRECTOR, only show users from their property
        if ($user->role->name !== 'DIRECTOR') {
            $query->where('property_id', $user->property_id);
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate(15);

        // Get properties for DIRECTOR users
        $properties = collect();
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)
                                ->where('is_active', true)
                                ->get();
        }

        // Get all roles
        $roles = Role::all();

        return view('Users.tenant.users.index', compact('users', 'properties', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only DIRECTOR and MANAGER roles can create users
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return redirect()->route('tenant.users.index')
                ->with('error', 'You do not have permission to create users.');
        }

        // Get properties based on user role
        $properties = collect();
        if ($user->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $user->tenant_id)
                                ->where('is_active', true)
                                ->get();
        } else {
            // MANAGER can only create users for their property
            $properties = Property::where('id', $user->property_id)->get();
        }

        // Get available roles based on user's role
        $availableRoles = $this->getAvailableRoles($user->role->name);

        return view('Users.tenant.users.create', compact('properties', 'availableRoles'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only DIRECTOR and MANAGER roles can create users
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER'])) {
            return redirect()->route('tenant.users.index')
                ->with('error', 'You do not have permission to create users.');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'property_id' => 'required|uuid|exists:properties,id',
            'role_id' => 'required|uuid|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to user's tenant
            $property = Property::where('id', $validated['property_id'])
                              ->where('tenant_id', $user->tenant_id)
                              ->firstOrFail();

            // If user is MANAGER, they can only create users for their property
            if ($user->role->name === 'MANAGER' && $validated['property_id'] !== $user->property_id) {
                throw ValidationException::withMessages([
                    'property_id' => 'You can only create users for your assigned property.'
                ]);
            }

            // Verify role is allowed
            $allowedRoles = $this->getAvailableRoles($user->role->name);
            $selectedRole = Role::findOrFail($validated['role_id']);
            
            if (!$allowedRoles->contains('id', $validated['role_id'])) {
                throw ValidationException::withMessages([
                    'role_id' => 'You cannot assign this role.'
                ]);
            }

            // Create the user
            $newUser = User::create([
                'tenant_id' => $user->tenant_id,
                'property_id' => $validated['property_id'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'full_name' => $validated['full_name'],
                'role_id' => $validated['role_id'],
                'phone' => $validated['phone'],
                'is_active' => $request->boolean('is_active', true),
                'mfa_enabled' => false,
            ]);

            DB::commit();

            return redirect()->route('tenant.users.show', $newUser->id)
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create user. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== $currentUser->tenant_id) {
            abort(403, 'Unauthorized access to user.');
        }

        // If not DIRECTOR, only show users from same property
        if ($currentUser->role->name !== 'DIRECTOR' && $user->property_id !== $currentUser->property_id) {
            abort(403, 'Unauthorized access to user.');
        }

        // Load relationships
        $user->load(['role', 'property', 'tenant']);

        return view('Users.tenant.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $currentUser = Auth::user();
        
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== $currentUser->tenant_id) {
            abort(403, 'Unauthorized access to user.');
        }

        // Permission checks
        if (!$this->canEditUser($currentUser, $user)) {
            return redirect()->route('tenant.users.show', $user->id)
                ->with('error', 'You do not have permission to edit this user.');
        }

        // Get properties based on user role
        $properties = collect();
        if ($currentUser->role->name === 'DIRECTOR') {
            $properties = Property::where('tenant_id', $currentUser->tenant_id)
                                ->where('is_active', true)
                                ->get();
        } else {
            $properties = Property::where('id', $currentUser->property_id)->get();
        }

        // Get available roles
        $availableRoles = $this->getAvailableRoles($currentUser->role->name);

        return view('Users.tenant.users.edit', compact('user', 'properties', 'availableRoles'));
    }

    /**
     * Update the specified user in storage
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== $currentUser->tenant_id) {
            abort(403, 'Unauthorized access to user.');
        }

        // Permission checks
        if (!$this->canEditUser($currentUser, $user)) {
            return redirect()->route('tenant.users.show', $user->id)
                ->with('error', 'You do not have permission to edit this user.');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'property_id' => 'required|uuid|exists:properties,id',
            'role_id' => 'required|uuid|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to user's tenant
            $property = Property::where('id', $validated['property_id'])
                              ->where('tenant_id', $currentUser->tenant_id)
                              ->firstOrFail();

            // If user is MANAGER, they can only assign to their property
            if ($currentUser->role->name === 'MANAGER' && $validated['property_id'] !== $currentUser->property_id) {
                throw ValidationException::withMessages([
                    'property_id' => 'You can only assign users to your assigned property.'
                ]);
            }

            // Verify role is allowed
            $allowedRoles = $this->getAvailableRoles($currentUser->role->name);
            if (!$allowedRoles->contains('id', $validated['role_id'])) {
                throw ValidationException::withMessages([
                    'role_id' => 'You cannot assign this role.'
                ]);
            }

            // Update user data
            $updateData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'property_id' => $validated['property_id'],
                'role_id' => $validated['role_id'],
                'is_active' => $request->boolean('is_active', $user->is_active),
            ];

            // Update password if provided
            if (!empty($validated['password'])) {
                $updateData['password_hash'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            DB::commit();

            return redirect()->route('tenant.users.show', $user->id)
                ->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update user. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage (soft delete)
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== $currentUser->tenant_id) {
            abort(403, 'Unauthorized access to user.');
        }

        // Only DIRECTOR can delete users
        if ($currentUser->role->name !== 'DIRECTOR') {
            return redirect()->route('tenant.users.show', $user->id)
                ->with('error', 'Only business directors can delete users.');
        }

        // Cannot delete yourself
        if ($user->id === $currentUser->id) {
            return redirect()->route('tenant.users.show', $user->id)
                ->with('error', 'You cannot delete your own account.');
        }

        try {
            DB::beginTransaction();

            // Soft delete the user
            $user->delete();

            DB::commit();

            return redirect()->route('tenant.users.index')
                ->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tenant.users.show', $user->id)
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $currentUser = Auth::user();
        
        // Ensure user belongs to the same tenant
        if ($user->tenant_id !== $currentUser->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to user.'
            ], 403);
        }

        // Permission checks
        if (!$this->canEditUser($currentUser, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to change user status.'
            ], 403);
        }

        // Cannot deactivate yourself
        if ($user->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own account.'
            ], 400);
        }

        try {
            $user->update(['is_active' => !$user->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!',
                'is_active' => $user->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status.'
            ], 500);
        }
    }

    /**
     * Get dashboard data for the authenticated user
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Load user with relationships
        $user->load(['role', 'property.tenant']);

        // Ensure user has a valid tenant
        if (!$user->tenant_id) {
            abort(403, 'User not associated with any tenant.');
        }

        // Directors should use the main dashboard instead
        if ($user->role->name === 'DIRECTOR') {
            return redirect()->route('dashboard');
        }

        // Get dashboard data based on user role
        $dashboardData = $this->getDashboardData($user);

        // Get view based on role
        $viewName = $this->getDashboardView($user->role->name);

        return view($viewName, compact('user', 'dashboardData'));
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function getDashboardStats()
    {
        $user = Auth::user();
        
        // Ensure user has a valid tenant
        if (!$user->tenant_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        $user->load(['role', 'property.tenant']);

        $dashboardData = $this->getDashboardData($user);

        return response()->json($dashboardData);
    }

    /**
     * Get available roles based on current user's role
     */
    private function getAvailableRoles($currentUserRole)
    {
        $roleHierarchy = [
            'DIRECTOR' => ['MANAGER', 'SUPERVISOR', 'ACCOUNTANT', 'BAR_TENDER', 'RECEPTIONIST', 'HOUSEKEEPER'],
            'MANAGER' => ['SUPERVISOR', 'ACCOUNTANT', 'BAR_TENDER', 'RECEPTIONIST', 'HOUSEKEEPER'],
        ];

        $allowedRoleNames = $roleHierarchy[$currentUserRole] ?? [];
        
        return Role::whereIn('name', $allowedRoleNames)->get();
    }

    /**
     * Check if current user can edit the target user
     */
    private function canEditUser($currentUser, $targetUser)
    {
        // DIRECTOR can edit anyone in their tenant
        if ($currentUser->role->name === 'DIRECTOR') {
            return true;
        }

        // MANAGER can edit users in their property (except other MANAGERs and DIRECTORs)
        if ($currentUser->role->name === 'MANAGER') {
            return $targetUser->property_id === $currentUser->property_id &&
                   !in_array($targetUser->role->name, ['DIRECTOR', 'MANAGER']);
        }

        return false;
    }

    /**
     * Get dashboard data based on user role
     */
    private function getDashboardData($user)
    {
        $data = [];

        // Ensure all queries are tenant-aware
        $tenantId = $user->tenant_id;

        switch ($user->role->name) {
            case 'DIRECTOR':
                $data = [
                    'total_properties' => Property::where('tenant_id', $tenantId)->count(),
                    'total_users' => User::where('tenant_id', $tenantId)->count(),
                    'active_users' => User::where('tenant_id', $tenantId)->where('is_active', true)->count(),
                    'total_buildings' => $user->property ? $user->property->buildings()->count() : 0,
                ];
                break;

            case 'MANAGER':
                // Ensure property belongs to user's tenant
                $propertyUsers = User::where('tenant_id', $tenantId)
                    ->where('property_id', $user->property_id)
                    ->count();
                    
                $activeUsers = User::where('tenant_id', $tenantId)
                    ->where('property_id', $user->property_id)
                    ->where('is_active', true)
                    ->count();
                    
                $data = [
                    'property_users' => $propertyUsers,
                    'active_users' => $activeUsers,
                    'buildings' => $user->property && $user->property->tenant_id === $tenantId ? $user->property->buildings()->count() : 0,
                    'total_rooms' => 0, // TODO: Implement when Room model is ready
                ];
                break;

            case 'SUPERVISOR':
                // Only count team members from same tenant and property
                $teamMembers = User::where('tenant_id', $tenantId)
                    ->where('property_id', $user->property_id)
                    ->whereHas('role', function($q) {
                        $q->whereIn('name', ['BAR_TENDER', 'RECEPTIONIST', 'HOUSEKEEPER']);
                    })->count();
                    
                $data = [
                    'team_members' => $teamMembers,
                    'daily_tasks' => 12, // TODO: Implement task system with tenant filtering
                    'completed_tasks' => 9, // TODO: Implement task system with tenant filtering
                    'active_shifts' => 6, // TODO: Implement shift system with tenant filtering
                ];
                break;

            case 'ACCOUNTANT':
                $data = [
                    'daily_revenue' => 2450, // TODO: Implement from actual transactions with tenant filtering
                    'monthly_revenue' => 68200, // TODO: Implement from actual transactions with tenant filtering
                    'outstanding_bills' => 24, // TODO: Implement from Invoice model with tenant filtering
                    'monthly_expenses' => 15800, // TODO: Implement from expense tracking with tenant filtering
                ];
                break;

            case 'BAR_TENDER':
                $data = [
                    'daily_orders' => 47, // TODO: Implement from POS system with tenant filtering
                    'daily_revenue' => 685, // TODO: Implement from POS system with tenant filtering
                    'active_tables' => 8, // TODO: Implement table management with tenant filtering
                    'low_stock_items' => 3, // TODO: Implement inventory system with tenant filtering
                ];
                break;

            case 'RECEPTIONIST':
                $data = [
                    'todays_arrivals' => 12, // TODO: Implement from reservations with tenant filtering
                    'todays_departures' => 8, // TODO: Implement from reservations with tenant filtering
                    'current_occupancy' => 85, // TODO: Implement from room management with tenant filtering
                    'guest_requests' => 5, // TODO: Implement request system with tenant filtering
                ];
                break;

            case 'HOUSEKEEPER':
                $data = [
                    'rooms_to_clean' => 18, // TODO: Implement from housekeeping schedule with tenant filtering
                    'completed_rooms' => 12, // TODO: Implement from housekeeping schedule with tenant filtering
                    'pending_inspections' => 3, // TODO: Implement inspection system with tenant filtering
                    'maintenance_issues' => 2, // TODO: Implement maintenance system with tenant filtering
                ];
                break;

            default:
                $data = [
                    'property_name' => ($user->property && $user->property->tenant_id === $tenantId) ? $user->property->name : 'No Property Assigned',
                    'role' => $user->role->name,
                    'tenant_name' => $user->tenant ? $user->tenant->name : 'Unknown Tenant',
                ];
                break;
        }

        return $data;
    }

    /**
     * Get dashboard view based on user role
     */
    private function getDashboardView($roleName)
    {
        $viewMapping = [
            'DIRECTOR' => 'Users.Dashboard', // Use main dashboard for directors
            'MANAGER' => 'Users.tenant.users.dashboards.manager',
            'SUPERVISOR' => 'Users.tenant.users.dashboards.supervisor',
            'ACCOUNTANT' => 'Users.tenant.users.dashboards.accountant',
            'BAR_TENDER' => 'Users.tenant.users.dashboards.bar_tender',
            'RECEPTIONIST' => 'Users.tenant.users.dashboards.receptionist',
            'HOUSEKEEPER' => 'Users.tenant.users.dashboards.housekeeper',
        ];

        return $viewMapping[$roleName] ?? 'Users.tenant.users.dashboards.default';
    }
}