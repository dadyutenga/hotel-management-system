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
    public function supervisorIndex(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        $query = HousekeepingTask::with(['property', 'room.roomType', 'assignedTo', 'creator', 'verifiedBy']);

        if ($user->property_id) {
            $query->where('property_id', $user->property_id);
        } else {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->latest('scheduled_date')->latest('scheduled_time')->paginate(20);

        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        $housekeepers = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->where('is_active', true)
            ->get();

        return view('Users.tenant.housekeeping.supervisor.index', compact('tasks', 'properties', 'housekeepers'));
    }

    public function supervisorCreate(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        $properties = Property::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get();

        $housekeepers = User::where('tenant_id', $user->tenant_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->where('is_active', true)
            ->get();

        $rooms = collect();
        if ($request->filled('property_id')) {
            $rooms = Room::where('property_id', $request->property_id)
                ->with('roomType')
                ->get();
        } elseif ($user->property_id) {
            $rooms = Room::where('property_id', $user->property_id)
                ->with('roomType')
                ->get();
        }

        return view('Users.tenant.housekeeping.supervisor.create', compact('properties', 'housekeepers', 'rooms'));
    }

    public function supervisorStore(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
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

            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

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

            return redirect()->route('supervisor.housekeeping.index')
                ->with('success', 'Housekeeping task created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    public function supervisorShow(HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        if ($task->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->load(['property', 'room.roomType', 'assignedTo', 'verifiedBy', 'creator', 'updater']);

        return view('Users.tenant.housekeeping.supervisor.show', compact('task'));
    }

    public function supervisorUpdate(Request $request, HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        if ($task->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        $validated = $request->validate([
            'room_id' => 'required|uuid|exists:rooms,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'task_type' => 'required|in:DAILY_CLEAN,DEEP_CLEAN,TURNDOWN,INSPECTION,OTHER',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED,VERIFIED,CANCELLED',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $housekeeper = User::where('id', $validated['assigned_to'])
                ->where('tenant_id', $user->tenant_id)
                ->whereHas('role', function($q) {
                    $q->where('name', 'HOUSEKEEPER');
                })
                ->firstOrFail();

            $updateData = [
                'room_id' => $validated['room_id'],
                'assigned_to' => $housekeeper->id,
                'task_type' => $validated['task_type'],
                'priority' => $validated['priority'],
                'status' => $validated['status'],
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => $user->id,
            ];

            $task->update($updateData);

            DB::commit();

            return redirect()->route('supervisor.housekeeping.index')
                ->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    public function supervisorDestroy(HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        if ($task->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        try {
            $task->delete();
            return redirect()->route('supervisor.housekeeping.index')
                ->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }

    public function verifyTask(Request $request, HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return back()->with('error', 'Unauthorized access.');
        }

        if ($task->property->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized access to this task.');
        }

        if ($task->status !== 'COMPLETED') {
            return back()->with('error', 'Only completed tasks can be verified.');
        }

        try {
            DB::beginTransaction();

            $task->update([
                'status' => 'VERIFIED',
                'verified_at' => now(),
                'verified_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            if (in_array($task->room->status, ['DIRTY', 'CLEAN'])) {
                $task->room->update(['status' => 'AVAILABLE']);
            }

            DB::commit();

            return back()->with('success', 'Task verified successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to verify task: ' . $e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'SUPERVISOR') {
            return back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'property_id' => 'required|uuid|exists:properties,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'task_type' => 'required|in:DAILY_CLEAN,DEEP_CLEAN,TURNDOWN,INSPECTION,OTHER',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
        ]);

        try {
            DB::beginTransaction();

            $property = Property::where('id', $validated['property_id'])
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();

            $dirtyRooms = Room::where('property_id', $property->id)
                ->where('status', 'DIRTY')
                ->get();

            if ($dirtyRooms->isEmpty()) {
                return back()->with('info', 'No dirty rooms found.');
            }

            $tasksCreated = 0;
            foreach ($dirtyRooms as $room) {
                $existingTask = HousekeepingTask::where('room_id', $room->id)
                    ->whereDate('scheduled_date', now())
                    ->whereIn('status', ['PENDING', 'IN_PROGRESS'])
                    ->exists();

                if (!$existingTask) {
                    HousekeepingTask::create([
                        'property_id' => $property->id,
                        'room_id' => $room->id,
                        'assigned_to' => $validated['assigned_to'],
                        'task_type' => $validated['task_type'],
                        'status' => 'PENDING',
                        'priority' => $validated['priority'],
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

    public function housekeeperIndex(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        $query = HousekeepingTask::with(['property', 'room.roomType', 'creator'])
            ->where('assigned_to', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['PENDING', 'IN_PROGRESS']);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }

        $tasks = $query->latest('scheduled_date')->latest('scheduled_time')->paginate(20);

        $stats = [
            'pending' => HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'PENDING')
                ->count(),
            'in_progress' => HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'IN_PROGRESS')
                ->count(),
            'completed_today' => HousekeepingTask::where('assigned_to', $user->id)
                ->where('status', 'COMPLETED')
                ->whereDate('completed_at', now())
                ->count(),
        ];

        return view('Users.tenant.housekeeping.housekeeper.index', compact('tasks', 'stats'));
    }

    public function housekeeperShow(HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        if ($task->assigned_to !== $user->id) {
            abort(403, 'You can only view tasks assigned to you.');
        }

        $task->load(['property', 'room.roomType', 'creator', 'verifiedBy']);

        return view('Users.tenant.housekeeping.housekeeper.show', compact('task'));
    }

    public function housekeeperManage(HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Unauthorized access.');
        }

        if ($task->assigned_to !== $user->id) {
            abort(403, 'You can only manage tasks assigned to you.');
        }

        $task->load(['property', 'room.roomType', 'creator']);

        return view('Users.tenant.housekeeping.housekeeper.manage', compact('task'));
    }

    public function startTask(Request $request, HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return back()->with('error', 'Unauthorized access.');
        }

        if ($task->assigned_to !== $user->id) {
            abort(403, 'You can only start tasks assigned to you.');
        }

        if ($task->status !== 'PENDING') {
            return back()->with('error', 'Only pending tasks can be started.');
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

    public function completeTask(Request $request, HousekeepingTask $task)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'HOUSEKEEPER') {
            return back()->with('error', 'Unauthorized access.');
        }

        if ($task->assigned_to !== $user->id) {
            abort(403, 'You can only complete tasks assigned to you.');
        }

        if (!in_array($task->status, ['PENDING', 'IN_PROGRESS'])) {
            return back()->with('error', 'Only pending or in-progress tasks can be completed.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'updated_by' => $user->id,
            ];

            if (!$task->started_at) {
                $updateData['started_at'] = now();
            }

            if ($request->filled('notes')) {
                $updateData['notes'] = $validated['notes'];
            }

            $task->update($updateData);

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
}
