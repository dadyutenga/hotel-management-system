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

        // Get dashboard data based on user role
        $dashboardData = $this->getDashboardData($user);

        // Get view based on role
        $viewName = $this->getDashboardView($user->role->name);

        return view($viewName, compact('user', 'dashboardData'));
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

        switch ($user->role->name) {
            case 'DIRECTOR':
                $data = [
                    'total_properties' => Property::where('tenant_id', $user->tenant_id)->count(),
                    'total_users' => User::where('tenant_id', $user->tenant_id)->count(),
                    'active_users' => User::where('tenant_id', $user->tenant_id)->where('is_active', true)->count(),
                    'total_buildings' => $user->property ? $user->property->buildings()->count() : 0,
                ];
                break;

            case 'MANAGER':
                $data = [
                    'property_users' => User::where('property_id', $user->property_id)->count(),
                    'active_users' => User::where('property_id', $user->property_id)->where('is_active', true)->count(),
                    'buildings' => $user->property ? $user->property->buildings()->count() : 0,
                ];
                break;

            default:
                $data = [
                    'property_name' => $user->property ? $user->property->name : 'No Property Assigned',
                    'role' => $user->role->name,
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
            'DIRECTOR' => 'Users.tenant.users.dashboards.director',
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