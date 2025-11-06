<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingTask;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HousekeepingController extends Controller
{
    // ===============================
    // SUPERVISOR METHODS
    // ===============================

    /**
     * Display a listing of housekeeping tasks (Supervisor view)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only SUPERVISOR can access
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $query = HousekeepingTask::with(['property', 'room', 'assignedTo', 'creator']);

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

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }

        // Filter by housekeeper
        if ($request->filled('housekeeper_id')) {
            $query->where('assigned_to', $request->housekeeper_id);
        }

        $tasks = $query->latest('scheduled_date')->latest('scheduled_time')->paginate(20);

        // Get properties for filter
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        // Get housekeepers for filter
        $housekeepers = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->where('is_active', true)
            ->get();

        return view('Users.tenant.housekeeping.supervisor.index', compact('tasks', 'properties', 'housekeepers'));
    }

    /**
     * Show the form for creating a new task (Supervisor)
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Only SUPERVISOR can create tasks
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

        // Get rooms if property is pre-selected
        $rooms = collect();
        if ($request->filled('property_id')) {
            $rooms = Room::where('property_id', $request->property_id)
                ->orderBy('room_number')
                ->get();
        }

        return view('Users.tenant.housekeeping.supervisor.create', compact('properties', 'housekeepers', 'rooms'));
    }

    /**
     * Store a newly created task (Supervisor)
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
            'room_id' => 'required|uuid|exists:rooms,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'task_type' => 'required|in:DAILY_CLEAN,DEEP_CLEAN,TURNDOWN,INSPECTION,OTHER',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // Verify assigned user is a housekeeper
            $housekeeper = User::where('id', $validated['assigned_to'])
                ->where('tenant_id', $user->tenant_id)
                ->whereHas('role', function($q) {
                    $q->where('name', 'HOUSEKEEPER');
                })
                ->firstOrFail();

            $task = HousekeepingTask::create([
                'property_id' => $validated['property_id'],
                'room_id' => $validated['room_id'],
                'assigned_to' => $validated['assigned_to'],
                'task_type' => $validated['task_type'],
                'priority' => $validated['priority'],
                'status' => 'PENDING',
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'],
                'notes' => $validated['notes'],
                'created_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.housekeeping.show', $task)
                ->with('success', 'Housekeeping task created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task (Supervisor)
     */
    public function show(HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        // Only SUPERVISOR can access
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $housekeeping->load(['property', 'room.roomType', 'assignedTo', 'verifiedBy', 'creator', 'updater']);

        // Get housekeepers for reassignment
        $housekeepers = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->where('is_active', true)
            ->get();

        return view('Users.tenant.housekeeping.supervisor.show', compact('housekeeping', 'housekeepers'));
    }

    /**
     * Show the form for editing the specified task (Supervisor)
     */
    public function edit(HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
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
        $rooms = Room::where('property_id', $housekeeping->property_id)
            ->orderBy('room_number')
            ->get();

        return view('Users.tenant.housekeeping.supervisor.edit', compact('housekeeping', 'properties', 'housekeepers', 'rooms'));
    }

    /**
     * Update the specified task (Supervisor)
     */
    public function update(Request $request, HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        // Only SUPERVISOR can update
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'room_id' => 'required|uuid|exists:rooms,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'task_type' => 'required|in:DAILY_CLEAN,DEEP_CLEAN,TURNDOWN,INSPECTION,OTHER',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // Verify assigned user is a housekeeper
            $housekeeper = User::where('id', $validated['assigned_to'])
                ->where('tenant_id', $user->tenant_id)
                ->whereHas('role', function($q) {
                    $q->where('name', 'HOUSEKEEPER');
                })
                ->firstOrFail();

            $housekeeping->update([
                'property_id' => $validated['property_id'],
                'room_id' => $validated['room_id'],
                'assigned_to' => $validated['assigned_to'],
                'task_type' => $validated['task_type'],
                'priority' => $validated['priority'],
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'],
                'notes' => $validated['notes'],
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('tenant.housekeeping.show', $housekeeping)
                ->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified task (Supervisor)
     */
    public function destroy(HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        // Only SUPERVISOR can delete
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        try {
            $housekeeping->delete();

            return redirect()->route('tenant.housekeeping.index')
                ->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Update task status (Supervisor)
     * Supervisors can only set VERIFIED (for completed tasks) or CANCELLED
     */
    public function updateStatus(Request $request, HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        // Only SUPERVISOR can update status
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Supervisor can only set VERIFIED or CANCELLED
        $validated = $request->validate([
            'status' => 'required|in:VERIFIED,CANCELLED',
        ]);

        try {
            // VERIFIED can only be set if task is COMPLETED
            if ($validated['status'] === 'VERIFIED' && $housekeeping->status !== 'COMPLETED') {
                return back()->with('error', 'Only completed tasks can be verified.');
            }

            $updateData = [
                'status' => $validated['status'],
                'updated_by' => $user->id,
            ];

            // Set verified_by if status is VERIFIED
            if ($validated['status'] === 'VERIFIED') {
                $updateData['verified_by'] = $user->id;
                $updateData['verified_at'] = now();
            }

            $housekeeping->update($updateData);

            return back()->with('success', 'Task status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Assign task to housekeeper (Supervisor)
     */
    public function assign(Request $request, HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Only SUPERVISOR can reassign
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|uuid|exists:users,id',
        ]);

        try {
            // Verify assigned user is a housekeeper
            $housekeeper = User::where('id', $validated['assigned_to'])
                ->where('tenant_id', $user->tenant_id)
                ->whereHas('role', function($q) {
                    $q->where('name', 'HOUSEKEEPER');
                })
                ->firstOrFail();

            $housekeeping->update([
                'assigned_to' => $validated['assigned_to'],
                'updated_by' => $user->id,
            ]);

            return back()->with('success', 'Task reassigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reassign task: ' . $e->getMessage());
        }
    }

    /**
     * Mark task as complete (Supervisor)
     */
    public function markComplete(HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Only SUPERVISOR can mark complete
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        try {
            $housekeeping->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'updated_by' => $user->id,
            ]);

            return back()->with('success', 'Task marked as completed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark task as complete: ' . $e->getMessage());
        }
    }

    /**
     * Create tasks for dirty rooms (Supervisor)
     */
    public function createForDirtyRooms(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'assigned_to' => 'required|uuid|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Verify property belongs to tenant
            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // Get all dirty rooms in the property
            $dirtyRooms = Room::where('property_id', $validated['property_id'])
                ->where('status', 'DIRTY')
                ->get();

            if ($dirtyRooms->isEmpty()) {
                return back()->with('error', 'No dirty rooms found in the selected property.');
            }

            $tasksCreated = 0;
            foreach ($dirtyRooms as $room) {
                HousekeepingTask::create([
                    'property_id' => $validated['property_id'],
                    'room_id' => $room->id,
                    'assigned_to' => $validated['assigned_to'],
                    'task_type' => 'DAILY_CLEAN',
                    'priority' => 'MEDIUM',
                    'status' => 'PENDING',
                    'scheduled_date' => now(),
                    'notes' => 'Auto-created for dirty room',
                    'created_by' => $user->id,
                ]);
                $tasksCreated++;
            }

            DB::commit();

            return back()->with('success', "{$tasksCreated} housekeeping tasks created for dirty rooms.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create tasks: ' . $e->getMessage());
        }
    }

    // ===============================
    // HOUSEKEEPER METHODS
    // ===============================

    /**
     * Get tasks for the current housekeeper
     */
    public function myTasks(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $query = HousekeepingTask::where('assigned_to', $user->id)
            ->with(['property', 'room.roomType']);

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
        // Don't filter by status if not specified - show all tasks

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }
        // Don't filter by date if not specified - show all tasks

        $tasks = $query->latest('scheduled_date')->latest('scheduled_time')->paginate(20);

        // Get properties for filter dropdown
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Users.tenant.housekeeping.housekeeper.index', compact('tasks', 'properties'));
    }

    /**
     * Get today's tasks for the current housekeeper
     */
    public function todayTasks(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        $tasks = HousekeepingTask::where('assigned_to', $user->id)
            ->whereDate('scheduled_date', now())
            ->with(['property', 'room.roomType'])
            ->latest('scheduled_time')
            ->get();

        return view('Users.tenant.housekeeping.housekeeper.today', compact('tasks'));
    }

    /**
     * Show a specific task for the housekeeper
     */
    public function showTask(HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER' || $task->assigned_to !== $user->id) {
            abort(403, 'You can only view your own tasks.');
        }

        $task->load(['property', 'room.roomType', 'assignedTo', 'verifiedBy', 'creator', 'updater']);

        return view('Users.tenant.housekeeping.housekeeper.show', compact('task'));
    }

    /**
     * Start a housekeeping task
     */
    public function startTask(HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER' || $task->assigned_to !== $user->id) {
            abort(403, 'You can only update your own tasks.');
        }

        if ($task->status !== 'PENDING') {
            return back()->with('error', 'Task cannot be started.');
        }

        try {
            $task->update([
                'status' => 'IN_PROGRESS',
                'started_at' => now(),
                'updated_by' => $user->id,
            ]);

            return back()->with('success', 'Task started successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start task: ' . $e->getMessage());
        }
    }

    /**
     * Complete a housekeeping task
     */
    public function completeTask(HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER' || $task->assigned_to !== $user->id) {
            abort(403, 'You can only update your own tasks.');
        }

        if ($task->status !== 'IN_PROGRESS') {
            return back()->with('error', 'Task must be in progress to complete.');
        }

        try {
            DB::beginTransaction();

            $task->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'updated_by' => $user->id,
            ]);

            // Update room status to CLEAN if it was DIRTY
            if ($task->room->status === 'DIRTY') {
                $task->room->update(['status' => 'CLEAN']);
            }

            DB::commit();

            return back()->with('success', 'Task completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete task: ' . $e->getMessage());
        }
    }

    /**
     * Update task progress
     */
    public function updateTaskProgress(Request $request, HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER' || $task->assigned_to !== $user->id) {
            abort(403, 'You can only update your own tasks.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        try {
            $updateData = [
                'updated_by' => $user->id,
            ];

            if ($request->filled('notes')) {
                $updateData['notes'] = $validated['notes'];
            }

            $task->update($updateData);

            return back()->with('success', 'Task progress updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update progress: ' . $e->getMessage());
        }
    }

    /**
     * Get housekeeper statistics
     */
    public function myStatistics(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Overall statistics
        $totalTasks = HousekeepingTask::where('assigned_to', $user->id)->count();
        $completedTasks = HousekeepingTask::where('assigned_to', $user->id)
            ->whereIn('status', ['COMPLETED', 'VERIFIED'])
            ->count();

        // Calculate completion rate
        $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        // Calculate average completion time
        $avgCompletionTime = HousekeepingTask::where('assigned_to', $user->id)
            ->where('status', 'COMPLETED')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, completed_at)) as avg_time')
            ->first()
            ->avg_time ?? 0;

        // Get task counts by type
        $tasksByType = HousekeepingTask::where('assigned_to', $user->id)
            ->whereIn('status', ['COMPLETED', 'VERIFIED'])
            ->selectRaw('task_type, COUNT(*) as count')
            ->groupBy('task_type')
            ->pluck('count', 'task_type')
            ->toArray();

        // Monthly breakdown (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $total = HousekeepingTask::where('assigned_to', $user->id)
                ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
                ->count();

            $completed = HousekeepingTask::where('assigned_to', $user->id)
                ->whereIn('status', ['COMPLETED', 'VERIFIED'])
                ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
                ->count();

            $inProgress = HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'IN_PROGRESS')
                ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
                ->count();

            $pending = HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'PENDING')
                ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
                ->count();

            $monthlyData[$monthKey] = [
                'total' => $total,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'pending' => $pending,
            ];
        }

        $statistics = [
            'overall' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'in_progress_tasks' => HousekeepingTask::where('assigned_to', $user->id)
                    ->where('status', 'IN_PROGRESS')
                    ->count(),
                'pending_tasks' => HousekeepingTask::where('assigned_to', $user->id)
                    ->where('status', 'PENDING')
                    ->count(),
                'completion_rate' => $completionRate,
                'average_completion_time' => round($avgCompletionTime),
                'tasks_by_type' => $tasksByType,
            ],
            'monthly' => $monthlyData,
        ];

        return view('Users.tenant.housekeeping.housekeeper.statistics', compact('statistics'));
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
}
