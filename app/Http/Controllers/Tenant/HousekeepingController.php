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
    /**
     * Display a listing of housekeeping tasks
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Authorization: DIRECTOR, MANAGER, SUPERVISOR, HOUSEKEEPER can access
        if (!in_array($user->role->name, [ 'SUPERVISOR', 'HOUSEKEEPER'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to access housekeeping management.');
        }

        $query = HousekeepingTask::with(['property', 'room', 'assignedTo', 'creator']);

        // Filter by tenant's properties
        if ($user->role->name === 'DIRECTOR') {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        } elseif ($user->role->name === 'MANAGER' && $user->property_id) {
            $query->where('property_id', $user->property_id);
        } elseif ($user->role->name === 'HOUSEKEEPER') {
            // Housekeepers see only their assigned tasks
            $query->where('assigned_to', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show pending and in-progress tasks
            $query->whereIn('status', ['PENDING', 'IN_PROGRESS']);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        } else {
            // Default: show today's tasks
            $query->whereDate('scheduled_date', now());
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $tasks = $query->latest('scheduled_date')->latest('scheduled_time')->paginate(20);

        // Get properties for filter
        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        return view('Users.tenant.housekeeping.index', compact('tasks', 'properties'));
    }

    /**
     * Show the form for creating a new task
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Only DIRECTOR, MANAGER, SUPERVISOR can create tasks
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create housekeeping tasks.');
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
                ->with('roomType')
                ->get();
        }

        return view('Users.tenant.housekeeping.create', compact('properties', 'housekeepers', 'rooms'));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You do not have permission to create housekeeping tasks.');
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

            $task = HousekeepingTask::create([
                'property_id' => $property->id,
                'room_id' => $validated['room_id'],
                'assigned_to' => $housekeeper->id,
                'task_type' => $validated['task_type'],
                'status' => 'PENDING',
                'priority' => $validated['priority'],
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'] ?? null,
                'notes' => $validated['notes'] ?? null,
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
     * Display the specified task
     */
    public function show(HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        // HOUSEKEEPER can only see their own tasks
        if ($user->role->name === 'HOUSEKEEPER' && $housekeeping->assigned_to !== $user->id) {
            abort(403, 'You can only view tasks assigned to you.');
        }

        $housekeeping->load(['property', 'room.roomType', 'assignedTo', 'verifiedBy', 'creator', 'updater']);

        return view('Users.tenant.housekeeping.show', compact('housekeeping'));
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        $validated = $request->validate([
            'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED,VERIFIED,CANCELLED',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $newStatus = $validated['status'];

            // Authorization checks
            if ($user->role->name === 'HOUSEKEEPER') {
                // Housekeepers can only update their own tasks
                if ($housekeeping->assigned_to !== $user->id) {
                    throw new \Exception('You can only update your own tasks.');
                }
                // Housekeepers can only mark as IN_PROGRESS or COMPLETED
                if (!in_array($newStatus, ['IN_PROGRESS', 'COMPLETED'])) {
                    throw new \Exception('You can only mark tasks as In Progress or Completed.');
                }
            }

            $updateData = ['status' => $newStatus, 'updated_by' => $user->id];

            // Set timestamps based on status
            if ($newStatus === 'IN_PROGRESS' && !$housekeeping->started_at) {
                $updateData['started_at'] = now();
            } elseif ($newStatus === 'COMPLETED' && !$housekeeping->completed_at) {
                $updateData['completed_at'] = now();
                
                // Update room status to CLEAN if it was DIRTY
                if ($housekeeping->room->status === 'DIRTY') {
                    $housekeeping->room->update(['status' => 'CLEAN']);
                }
            } elseif ($newStatus === 'VERIFIED') {
                $updateData['verified_at'] = now();
                $updateData['verified_by'] = $user->id;
                
                // Make room available
                if (in_array($housekeeping->room->status, ['DIRTY', 'CLEAN'])) {
                    $housekeeping->room->update(['status' => 'AVAILABLE']);
                }
            }

            if ($request->filled('notes')) {
                $updateData['notes'] = $validated['notes'];
            }

            $housekeeping->update($updateData);

            DB::commit();

            return back()->with('success', 'Task status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Assign task to housekeeper
     */
    public function assign(Request $request, HousekeepingTask $housekeeping)
    {
        $user = Auth::user();
        
        // Only DIRECTOR, MANAGER, SUPERVISOR can reassign
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return back()->with('error', 'You do not have permission to reassign tasks.');
        }

        // Verify tenant ownership
        if ($housekeeping->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|uuid|exists:users,id',
        ]);

        try {
            // Verify new assignee is a housekeeper
            $housekeeper = User::where('id', $validated['assigned_to'])
                ->where('tenant_id', $user->tenant_id)
                ->whereHas('role', function($q) {
                    $q->where('name', 'HOUSEKEEPER');
                })
                ->firstOrFail();

            $housekeeping->update([
                'assigned_to' => $housekeeper->id,
                'updated_by' => $user->id,
            ]);

            return back()->with('success', 'Task reassigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reassign task: ' . $e->getMessage());
        }
    }

    /**
     * Create tasks for dirty rooms
     */
    public function createForDirtyRooms(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role->name, ['DIRECTOR', 'MANAGER', 'SUPERVISOR'])) {
            return back()->with('error', 'You do not have permission to create tasks.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'assigned_to' => 'required|uuid|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            // Get all dirty rooms
            $dirtyRooms = Room::where('property_id', $property->id)
                ->where('status', 'DIRTY')
                ->get();

            if ($dirtyRooms->isEmpty()) {
                return back()->with('info', 'No dirty rooms found.');
            }

            $tasksCreated = 0;
            foreach ($dirtyRooms as $room) {
                // Check if task already exists
                $existingTask = HousekeepingTask::where('room_id', $room->id)
                    ->whereDate('scheduled_date', now())
                    ->whereIn('status', ['PENDING', 'IN_PROGRESS'])
                    ->exists();

                if (!$existingTask) {
                    HousekeepingTask::create([
                        'property_id' => $property->id,
                        'room_id' => $room->id,
                        'assigned_to' => $validated['assigned_to'],
                        'task_type' => 'DAILY_CLEAN',
                        'status' => 'PENDING',
                        'priority' => 'HIGH',
                        'scheduled_date' => now(),
                        'created_by' => $user->id,
                    ]);
                    $tasksCreated++;
                }
            }

            DB::commit();

            return back()->with('success', "{$tasksCreated} housekeeping tasks created for dirty rooms.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create tasks: ' . $e->getMessage());
        }
    }

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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['PENDING', 'IN_PROGRESS']);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        } else {
            $query->whereDate('scheduled_date', '>=', now());
        }

        $tasks = $query->latest('scheduled_date')->latest('scheduled_time')->paginate(20);

        return view('Users.tenant.housekeeping.housekeeper.index', compact('tasks'));
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

        $task->load(['property', 'room.roomType', 'assignedTo', 'creator', 'updater']);

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
            'progress' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            $updateData = [
                'progress_percentage' => $validated['progress'],
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
     * Update room status from task
     */
    public function updateRoomStatus(Request $request, HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER' || $task->assigned_to !== $user->id) {
            abort(403, 'You can only update your own tasks.');
        }

        $validated = $request->validate([
            'room_status' => 'required|in:CLEAN,DIRTY,OCCUPIED,OUT_OF_ORDER',
        ]);

        try {
            $task->room->update(['status' => $validated['room_status']]);

            return back()->with('success', 'Room status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update room status: ' . $e->getMessage());
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

        // Get statistics for the current month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $stats = [
            'total_tasks' => HousekeepingTask::where('assigned_to', $user->id)
                ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
                ->count(),

            'completed_tasks' => HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'COMPLETED')
                ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
                ->count(),

            'pending_tasks' => HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'PENDING')
                ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
                ->count(),

            'in_progress_tasks' => HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'IN_PROGRESS')
                ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
                ->count(),

            'average_completion_time' => HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'COMPLETED')
                ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
                ->whereNotNull('started_at')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, completed_at)) as avg_time')
                ->first()
                ->avg_time ?? 0,
        ];

        return view('Users.tenant.housekeeping.housekeeper.statistics', compact('stats'));
    }

    /**
     * Report an issue with a task
     */
    public function reportIssue(Request $request, HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER' || $task->assigned_to !== $user->id) {
            abort(403, 'You can only report issues for your own tasks.');
        }

        $validated = $request->validate([
            'issue_type' => 'required|in:EQUIPMENT_MISSING,SUPPLIES_NEEDED,ROOM_DAMAGE,OTHER',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
        ]);

        try {
            // Here you would typically create an issue/ticket record
            // For now, we'll just update the task notes
            $notes = $task->notes ? $task->notes . "\n\n" : '';
            $notes .= "ISSUE REPORTED " . now() . ":\n";
            $notes .= "Type: {$validated['issue_type']}\n";
            $notes .= "Priority: {$validated['priority']}\n";
            $notes .= "Description: {$validated['description']}\n";
            $notes .= "Reported by: {$user->full_name}";

            $task->update([
                'notes' => $notes,
                'updated_by' => $user->id,
            ]);

            return back()->with('success', 'Issue reported successfully. Maintenance will be notified.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to report issue: ' . $e->getMessage());
        }
    }
}
