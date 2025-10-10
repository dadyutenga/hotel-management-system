<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\User;
use App\Models\RoomInspection;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $query = HousekeepingTask::where('property_id', $property->id ?? null);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $query->with(['room.floor.building', 'assignedTo', 'verifiedBy']);

        $sortBy = $request->get('sort_by', 'scheduled_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(20);

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'room_id' => 'required|uuid|exists:rooms,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'task_type' => 'required|in:DAILY_CLEAN,DEEP_CLEAN,TURNDOWN,INSPECTION,OTHER',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::where('id', $request->room_id)
            ->where('property_id', $property->id)
            ->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found in your property'
            ], 404);
        }

        $housekeeper = User::where('id', $request->assigned_to)
            ->where('tenant_id', $tenant->id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->first();

        if (!$housekeeper) {
            return response()->json([
                'success' => false,
                'message' => 'Housekeeper not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $task = HousekeepingTask::create([
                'property_id' => $property->id,
                'room_id' => $room->id,
                'assigned_to' => $housekeeper->id,
                'task_type' => $request->task_type,
                'priority' => $request->priority,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'notes' => $request->notes,
                'status' => 'PENDING',
                'created_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Housekeeping task created successfully',
                'task' => $task->load(['room', 'assignedTo'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating housekeeping task: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $property = $user->property;

        $task = HousekeepingTask::where('id', $id)
            ->where('property_id', $property->id)
            ->with([
                'room.floor.building',
                'assignedTo',
                'verifiedBy',
                'creator',
                'updater'
            ])
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $task = HousekeepingTask::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'assigned_to' => 'sometimes|required|uuid|exists:users,id',
            'task_type' => 'sometimes|required|in:DAILY_CLEAN,DEEP_CLEAN,TURNDOWN,INSPECTION,OTHER',
            'priority' => 'sometimes|required|in:LOW,MEDIUM,HIGH',
            'scheduled_date' => 'sometimes|required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'status' => 'sometimes|required|in:PENDING,IN_PROGRESS,COMPLETED,VERIFIED,CANCELLED',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'updated_by' => $user->id,
            ];

            if ($request->has('assigned_to')) {
                $updateData['assigned_to'] = $request->assigned_to;
            }
            if ($request->has('task_type')) {
                $updateData['task_type'] = $request->task_type;
            }
            if ($request->has('priority')) {
                $updateData['priority'] = $request->priority;
            }
            if ($request->has('scheduled_date')) {
                $updateData['scheduled_date'] = $request->scheduled_date;
            }
            if ($request->has('scheduled_time')) {
                $updateData['scheduled_time'] = $request->scheduled_time;
            }
            if ($request->has('notes')) {
                $updateData['notes'] = $request->notes;
            }
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }

            $task->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'task' => $task->load(['room', 'assignedTo'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating task: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyTask(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $task = HousekeepingTask::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        if ($task->status !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed tasks can be verified'
            ], 400);
        }

        try {
            $task->update([
                'status' => 'VERIFIED',
                'verified_at' => now(),
                'verified_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task verified successfully',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            \Log::error('Error verifying task: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error verifying task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelTask(Request $request, $id)
    {
        $user = Auth::user();
        $property = $user->property;

        $task = HousekeepingTask::where('id', $id)
            ->where('property_id', $property->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        if (in_array($task->status, ['COMPLETED', 'VERIFIED', 'CANCELLED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel task in current status'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $task->update([
                'status' => 'CANCELLED',
                'notes' => $task->notes . "\n\nCancelled: " . ($request->reason ?? 'No reason provided'),
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task cancelled successfully',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            \Log::error('Error cancelling task: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHousekeepers()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $property = $user->property;

        $housekeepers = User::where('tenant_id', $tenant->id)
            ->where('property_id', $property->id)
            ->where('is_active', true)
            ->whereHas('role', function($q) {
                $q->where('name', 'HOUSEKEEPER');
            })
            ->select('id', 'full_name', 'email', 'phone')
            ->get();

        return response()->json([
            'success' => true,
            'housekeepers' => $housekeepers
        ]);
    }

    public function createInspection(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'room_id' => 'required|uuid|exists:rooms,id',
            'inspection_date' => 'required|date',
            'cleanliness_score' => 'required|integer|min:1|max:10',
            'maintenance_score' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::where('id', $request->room_id)
            ->where('property_id', $property->id)
            ->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found in your property'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $inspection = RoomInspection::create([
                'room_id' => $room->id,
                'inspector_id' => $user->id,
                'inspection_date' => $request->inspection_date,
                'cleanliness_score' => $request->cleanliness_score,
                'maintenance_score' => $request->maintenance_score,
                'notes' => $request->notes,
            ]);

            if ($request->maintenance_score < 5 && $request->filled('maintenance_issues')) {
                MaintenanceRequest::create([
                    'property_id' => $property->id,
                    'room_id' => $room->id,
                    'issue_type' => 'INSPECTION_ISSUE',
                    'description' => $request->maintenance_issues,
                    'reported_by' => $user->id,
                    'status' => 'OPEN',
                    'priority' => $request->maintenance_score < 3 ? 'URGENT' : 'HIGH',
                    'reported_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room inspection created successfully',
                'inspection' => $inspection->load(['room', 'inspector'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating inspection: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating inspection: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkAssignTasks(Request $request)
    {
        $user = Auth::user();
        $property = $user->property;

        $validator = Validator::make($request->all(), [
            'room_ids' => 'required|array',
            'room_ids.*' => 'uuid|exists:rooms,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'task_type' => 'required|in:DAILY_CLEAN,DEEP_CLEAN,TURNDOWN,INSPECTION,OTHER',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $tasks = [];
            foreach ($request->room_ids as $roomId) {
                $room = Room::where('id', $roomId)
                    ->where('property_id', $property->id)
                    ->first();

                if ($room) {
                    $task = HousekeepingTask::create([
                        'property_id' => $property->id,
                        'room_id' => $room->id,
                        'assigned_to' => $request->assigned_to,
                        'task_type' => $request->task_type,
                        'priority' => $request->priority,
                        'scheduled_date' => $request->scheduled_date,
                        'scheduled_time' => $request->scheduled_time,
                        'status' => 'PENDING',
                        'created_by' => $user->id,
                    ]);
                    $tasks[] = $task;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($tasks) . ' tasks created successfully',
                'tasks' => $tasks
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error bulk assigning tasks: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating tasks: ' . $e->getMessage()
            ], 500);
        }
    }
}
