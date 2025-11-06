<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    // ===============================
    // SUPERVISOR METHODS
    // ===============================

    /**
     * Display a listing of maintenance requests (Supervisor view)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only SUPERVISOR can access
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $query = MaintenanceRequest::with(['property', 'room', 'reportedBy', 'assignedStaff']);

        // Filter by tenant's properties
        $query->whereHas('property', function($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        });

        // Filter by property if specified
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->latest('reported_at')->paginate(20);

        // Get properties for filter
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('Users.tenant.maintenance.supervisor.index', compact('requests', 'properties'));
    }

    /**
     * Show the form for creating a new maintenance request (Supervisor)
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Only SUPERVISOR can create requests
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Get properties
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        // Get housekeepers for assignment
        $housekeepers = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->where('is_active', true)
            ->get();

        // Get rooms if property is pre-selected
        $rooms = collect();
        if ($request->filled('property_id')) {
            $rooms = Room::where('property_id', $request->property_id)
                ->orderBy('room_number')
                ->get();
        }

        return view('Users.tenant.maintenance.supervisor.create', compact('properties', 'housekeepers', 'rooms'));
    }

    /**
     * Store a newly created maintenance request (Supervisor)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'location_details' => 'nullable|string|max:255',
            'issue_type' => 'required|string|max:100',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'housekeeper_ids' => 'nullable|array',
            'housekeeper_ids.*' => 'uuid|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            $maintenanceRequest = MaintenanceRequest::create([
                'property_id' => $validated['property_id'],
                'room_id' => $validated['room_id'] ?? null,
                'location_details' => $validated['location_details'] ?? null,
                'issue_type' => $validated['issue_type'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'status' => 'OPEN',
                'reported_by' => $user->id,
                'reported_at' => now(),
            ]);

            // Assign housekeepers if selected
            if (!empty($validated['housekeeper_ids'])) {
                $housekeeperData = [];
                foreach ($validated['housekeeper_ids'] as $housekeeperId) {
                    $housekeeperData[$housekeeperId] = ['assigned_at' => now()];
                }
                $maintenanceRequest->assignedStaff()->attach($housekeeperData);
                $maintenanceRequest->update(['status' => 'ASSIGNED']);
            }

            // Update room status if maintenance is urgent and room is specified
            if ($validated['priority'] === 'URGENT' && isset($validated['room_id'])) {
                $room = Room::find($validated['room_id']);
                if ($room && in_array($room->status, ['VACANT', 'CLEAN'])) {
                    $room->update(['status' => 'OUT_OF_ORDER']);
                }
            }

            DB::commit();

            return redirect()->route('tenant.maintenance.show', $maintenanceRequest)
                ->with('success', 'Maintenance request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the full error for debugging
            \Log::error('Maintenance request creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'data' => $validated
            ]);
            
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create maintenance request: ' . $e->getMessage()])
                ->with('error', 'Failed to create maintenance request: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
        }
    }

    /**
     * Display the specified maintenance request (Supervisor)
     */
    public function show(MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        // Only SUPERVISOR can access
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $maintenance->load(['property', 'room.roomType', 'reportedBy', 'assignedStaff']);

        // Get housekeepers for assignment
        $housekeepers = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->where('is_active', true)
            ->get();

        $maintenanceRequest = $maintenance;

        return view('Users.tenant.maintenance.supervisor.show', compact('maintenanceRequest', 'housekeepers'));
    }

    /**
     * Show the form for editing the specified maintenance request (Supervisor)
     */
    public function edit(MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        // Only SUPERVISOR can edit
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Get properties
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        // Get housekeepers
        $housekeepers = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->where('is_active', true)
            ->get();

        // Get rooms for the property
        $rooms = Room::where('property_id', $maintenance->property_id)
            ->orderBy('room_number')
            ->get();

        $maintenance->load(['assignedStaff']);

        $maintenanceRequest = $maintenance;

        return view('Users.tenant.maintenance.supervisor.edit', compact('maintenanceRequest', 'properties', 'housekeepers', 'rooms'));
    }

    /**
     * Update the specified maintenance request (Supervisor)
     */
    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        // Only SUPERVISOR can update
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'location_details' => 'nullable|string|max:255',
            'issue_type' => 'required|string|max:100',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'housekeeper_ids' => 'nullable|array',
            'housekeeper_ids.*' => 'uuid|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            $maintenance->update([
                'property_id' => $validated['property_id'],
                'room_id' => $validated['room_id'] ?? null,
                'location_details' => $validated['location_details'] ?? null,
                'issue_type' => $validated['issue_type'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
            ]);

            // Update assigned housekeepers
            if (isset($validated['housekeeper_ids'])) {
                $housekeeperData = [];
                foreach ($validated['housekeeper_ids'] as $housekeeperId) {
                    // Keep existing assigned_at if already assigned
                    $existingPivot = $maintenance->assignedStaff()->where('user_id', $housekeeperId)->first();
                    $assignedAt = $existingPivot ? $existingPivot->pivot->assigned_at : now();
                    $housekeeperData[$housekeeperId] = ['assigned_at' => $assignedAt];
                }
                $maintenance->assignedStaff()->sync($housekeeperData);

                // Update status based on assignment
                if (count($housekeeperData) > 0 && $maintenance->status === 'OPEN') {
                    $maintenance->update(['status' => 'ASSIGNED']);
                }
            } else {
                $maintenance->assignedStaff()->detach();
                if ($maintenance->status === 'ASSIGNED') {
                    $maintenance->update(['status' => 'OPEN']);
                }
            }

            DB::commit();

            return redirect()->route('tenant.maintenance.show', $maintenance)
                ->with('success', 'Maintenance request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update maintenance request: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified maintenance request (Supervisor)
     */
    public function destroy(MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        // Only SUPERVISOR can delete
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        try {
            $maintenance->delete();

            return redirect()->route('tenant.maintenance.index')
                ->with('success', 'Maintenance request deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete maintenance request: ' . $e->getMessage());
        }
    }

    /**
     * Update maintenance request status (Supervisor)
     */
    public function updateStatus(Request $request, MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        // Only SUPERVISOR can update status
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'status' => 'required|in:OPEN,ASSIGNED,IN_PROGRESS,ON_HOLD,COMPLETED,CANCELLED',
            'resolution_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $updateData = ['status' => $validated['status']];

            // Set timestamps based on status
            if ($validated['status'] === 'IN_PROGRESS' && !$maintenance->started_at) {
                $updateData['started_at'] = now();
            } elseif ($validated['status'] === 'COMPLETED' && !$maintenance->completed_at) {
                $updateData['completed_at'] = now();
                
                // Update room status if it was out of order for maintenance
                if ($maintenance->room && $maintenance->room->status === 'OUT_OF_ORDER') {
                    $maintenance->room->update(['status' => 'DIRTY']);
                }
            }

            if ($request->filled('resolution_notes')) {
                $updateData['resolution_notes'] = $validated['resolution_notes'];
            }

            $maintenance->update($updateData);

            DB::commit();

            return back()->with('success', 'Maintenance request status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Assign housekeepers to maintenance request (Supervisor)
     */
    public function assignStaff(Request $request, MaintenanceRequest $maintenance)
    {
        $user = Auth::user();
        
        // Only SUPERVISOR can assign
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Verify tenant ownership
        if ($maintenance->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this maintenance request.');
        }

        $validated = $request->validate([
            'housekeeper_ids' => 'required|array',
            'housekeeper_ids.*' => 'uuid|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify all are housekeepers
            $housekeepers = User::where('tenant_id', $user->tenant_id)
                ->whereHas('role', function($q) {
                    $q->where('name', 'HOUSEKEEPER');
                })
                ->whereIn('id', $validated['housekeeper_ids'])
                ->get();

            if ($housekeepers->count() !== count($validated['housekeeper_ids'])) {
                return back()->with('error', 'Some selected users are not housekeepers.');
            }

            // Assign with timestamp
            $housekeeperData = [];
            foreach ($validated['housekeeper_ids'] as $housekeeperId) {
                // Keep existing assigned_at if already assigned
                $existingPivot = $maintenance->assignedStaff()->where('user_id', $housekeeperId)->first();
                $assignedAt = $existingPivot ? $existingPivot->pivot->assigned_at : now();
                $housekeeperData[$housekeeperId] = ['assigned_at' => $assignedAt];
            }
            $maintenance->assignedStaff()->sync($housekeeperData);

            // Update status to ASSIGNED if it was OPEN
            if ($maintenance->status === 'OPEN') {
                $maintenance->update(['status' => 'ASSIGNED']);
            }

            DB::commit();

            return back()->with('success', 'Housekeepers assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign housekeepers: ' . $e->getMessage());
        }
    }

    /**
     * Get rooms by property (AJAX helper)
     */
    public function getRoomsByProperty($propertyId)
    {
        $user = Auth::user();
        
        $property = Property::where('id', $propertyId)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $rooms = Room::where('property_id', $propertyId)
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'status']);

        return response()->json($rooms);
    }

    // ===============================
    // HOUSEKEEPER METHODS
    // ===============================

    /**
     * Get maintenance tasks for the current housekeeper
     */
    public function myTasks(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $query = MaintenanceRequest::whereHas('assignedStaff', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['property', 'room.roomType', 'reportedBy']);

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest('reported_at')->paginate(20);

        // Get statistics
        $stats = [
            'OPEN' => MaintenanceRequest::whereHas('assignedStaff', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereIn('status', ['OPEN', 'ASSIGNED'])
                ->count(),
            'in_progress' => MaintenanceRequest::whereHas('assignedStaff', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('status', 'IN_PROGRESS')
                ->count(),
            'completed' => MaintenanceRequest::whereHas('assignedStaff', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('status', 'COMPLETED')
                ->count(),
        ];

        // Get properties for filter dropdown
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Users.tenant.maintenance.housekeeper.index', compact('tasks', 'properties', 'stats'));
    }

    /**
     * Get today's maintenance tasks for the current housekeeper
     */
    public function todayTasks(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $baseQuery = MaintenanceRequest::whereHas('assignedStaff', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereDate('reported_at', now())
            ->with(['property', 'room.roomType', 'reportedBy', 'assignedStaff']);

        // OPEN tasks (OPEN or ASSIGNED status)
        $OPENTasks = (clone $baseQuery)
            ->whereIn('status', ['OPEN', 'ASSIGNED'])
            ->latest('reported_at')
            ->get();

        // In Progress tasks
        $inProgressTasks = (clone $baseQuery)
            ->where('status', 'IN_PROGRESS')
            ->latest('reported_at')
            ->get();

        // Completed tasks (completed today)
        $completedTasks = (clone $baseQuery)
            ->where('status', 'COMPLETED')
            ->whereDate('completed_at', now())
            ->latest('completed_at')
            ->get();

        return view('Users.tenant.maintenance.housekeeper.today', compact('OPENTasks', 'inProgressTasks', 'completedTasks'));
    }

    /**
     * Show a specific maintenance request for the housekeeper
     */
    public function showTask(MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            abort(403, 'You can only view maintenance tasks assigned to you.');
        }

        // Check if this housekeeper is assigned to this request
        if (!$maintenanceRequest->assignedStaff()->where('user_id', $user->id)->exists()) {
            abort(403, 'You can only view your own maintenance tasks.');
        }

        $maintenanceRequest->load(['property', 'room.roomType', 'reportedBy', 'assignedStaff']);

        $task = $maintenanceRequest;

        return view('Users.tenant.maintenance.housekeeper.show', compact('task'));
    }

    /**
     * Start a maintenance request
     */
    public function startTask(MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            abort(403, 'You can only update your own tasks.');
        }

        // Check if assigned
        if (!$maintenanceRequest->assignedStaff()->where('user_id', $user->id)->exists()) {
            abort(403, 'You can only update your own maintenance tasks.');
        }

        if (!in_array($maintenanceRequest->status, ['OPEN', 'ASSIGNED'])) {
            return back()->with('error', 'Request cannot be started.');
        }

        try {
            $maintenanceRequest->update([
                'status' => 'IN_PROGRESS',
                'started_at' => now(),
            ]);

            return back()->with('success', 'Maintenance request started successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start request: ' . $e->getMessage());
        }
    }

    /**
     * Complete a maintenance request
     */
    public function completeTask(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            abort(403, 'You can only update your own tasks.');
        }

        // Check if assigned
        if (!$maintenanceRequest->assignedStaff()->where('user_id', $user->id)->exists()) {
            abort(403, 'You can only update your own maintenance tasks.');
        }

        if ($maintenanceRequest->status !== 'IN_PROGRESS') {
            return back()->with('error', 'Request must be in progress to complete.');
        }

        $validated = $request->validate([
            'resolution_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $maintenanceRequest->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'resolution_notes' => $validated['resolution_notes'] ?? null,
            ]);

            // Update room status to DIRTY if it was OUT_OF_ORDER for maintenance
            if ($maintenanceRequest->room && $maintenanceRequest->room->status === 'OUT_OF_ORDER') {
                $maintenanceRequest->room->update(['status' => 'DIRTY']);
            }

            DB::commit();

            return back()->with('success', 'Maintenance request completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete request: ' . $e->getMessage());
        }
    }
}
