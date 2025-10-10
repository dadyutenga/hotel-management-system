<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingTask;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HousekeeperController extends Controller
{
    public function myTasks(Request $request)
    {
        $user = Auth::user();

        $query = HousekeepingTask::where('assigned_to', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_date', $request->date);
        } else {
            $query->whereDate('scheduled_date', '>=', today());
        }

        $query->with(['room.floor.building', 'property']);

        $sortBy = $request->get('sort_by', 'scheduled_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(20);

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    public function todayTasks()
    {
        $user = Auth::user();

        $tasks = HousekeepingTask::where('assigned_to', $user->id)
            ->whereDate('scheduled_date', today())
            ->with(['room.floor.building', 'property'])
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_time', 'asc')
            ->get();

        $summary = [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'PENDING')->count(),
            'in_progress' => $tasks->where('status', 'IN_PROGRESS')->count(),
            'completed' => $tasks->where('status', 'COMPLETED')->count(),
        ];

        return response()->json([
            'success' => true,
            'tasks' => $tasks,
            'summary' => $summary
        ]);
    }

    public function showTask($id)
    {
        $user = Auth::user();

        $task = HousekeepingTask::where('id', $id)
            ->where('assigned_to', $user->id)
            ->with([
                'room.floor.building',
                'property',
                'verifiedBy',
                'creator'
            ])
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or not assigned to you'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    public function startTask(Request $request, $id)
    {
        $user = Auth::user();

        $task = HousekeepingTask::where('id', $id)
            ->where('assigned_to', $user->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or not assigned to you'
            ], 404);
        }

        if ($task->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending tasks can be started'
            ], 400);
        }

        try {
            $task->update([
                'status' => 'IN_PROGRESS',
                'started_at' => now(),
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            \Log::error('Error starting task: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error starting task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeTask(Request $request, $id)
    {
        $user = Auth::user();

        $task = HousekeepingTask::where('id', $id)
            ->where('assigned_to', $user->id)
            ->with('room')
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or not assigned to you'
            ], 404);
        }

        if (!in_array($task->status, ['PENDING', 'IN_PROGRESS'])) {
            return response()->json([
                'success' => false,
                'message' => 'Task cannot be completed in current status'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'completion_notes' => 'nullable|string',
            'issues_found' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $notes = $task->notes;
            if ($request->filled('completion_notes')) {
                $notes .= "\n\nCompletion Notes: " . $request->completion_notes;
            }
            if ($request->filled('issues_found')) {
                $notes .= "\n\nIssues Found: " . $request->issues_found;
            }

            $task->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
                'notes' => $notes,
                'updated_by' => $user->id,
            ]);

            if (in_array($task->task_type, ['DAILY_CLEAN', 'DEEP_CLEAN'])) {
                $task->room->update(['status' => 'CLEAN']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error completing task: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error completing task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTaskProgress(Request $request, $id)
    {
        $user = Auth::user();

        $task = HousekeepingTask::where('id', $id)
            ->where('assigned_to', $user->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or not assigned to you'
            ], 404);
        }

        if ($task->status !== 'IN_PROGRESS') {
            return response()->json([
                'success' => false,
                'message' => 'Can only update tasks that are in progress'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->filled('notes')) {
                $task->update([
                    'notes' => $task->notes . "\n\nProgress Update: " . $request->notes,
                    'updated_by' => $user->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Task progress updated',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating task progress: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating progress: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRoomStatus(Request $request, $taskId)
    {
        $user = Auth::user();

        $task = HousekeepingTask::where('id', $taskId)
            ->where('assigned_to', $user->id)
            ->with('room')
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or not assigned to you'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'room_status' => 'required|in:VACANT,OCCUPIED,DIRTY,CLEAN,OUT_OF_ORDER',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $task->room->update([
                'status' => $request->room_status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Room status updated successfully',
                'room' => $task->room
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating room status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating room status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myStatistics(Request $request)
    {
        $user = Auth::user();

        $startDate = $request->get('start_date', today()->subDays(30));
        $endDate = $request->get('end_date', today());

        $tasks = HousekeepingTask::where('assigned_to', $user->id)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->get();

        $statistics = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'COMPLETED')->count(),
            'verified_tasks' => $tasks->where('status', 'VERIFIED')->count(),
            'pending_tasks' => $tasks->where('status', 'PENDING')->count(),
            'in_progress_tasks' => $tasks->where('status', 'IN_PROGRESS')->count(),
            'cancelled_tasks' => $tasks->where('status', 'CANCELLED')->count(),
            'completion_rate' => $tasks->count() > 0 
                ? round(($tasks->whereIn('status', ['COMPLETED', 'VERIFIED'])->count() / $tasks->count()) * 100, 2)
                : 0,
            'tasks_by_type' => [
                'daily_clean' => $tasks->where('task_type', 'DAILY_CLEAN')->count(),
                'deep_clean' => $tasks->where('task_type', 'DEEP_CLEAN')->count(),
                'turndown' => $tasks->where('task_type', 'TURNDOWN')->count(),
                'inspection' => $tasks->where('task_type', 'INSPECTION')->count(),
                'other' => $tasks->where('task_type', 'OTHER')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }

    public function reportIssue(Request $request, $taskId)
    {
        $user = Auth::user();

        $task = HousekeepingTask::where('id', $taskId)
            ->where('assigned_to', $user->id)
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found or not assigned to you'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'issue_description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $task->update([
                'notes' => $task->notes . "\n\nISSUE REPORTED: " . $request->issue_description,
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Issue reported successfully',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            \Log::error('Error reporting issue: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error reporting issue: ' . $e->getMessage()
            ], 500);
        }
    }
}
