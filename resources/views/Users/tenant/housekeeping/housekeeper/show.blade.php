@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Task Details</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Task Information</h2>
                <div class="space-y-3">
                    <div>
                        <label class="font-medium">Room:</label>
                        <p>{{ $task->room->room_number }} ({{ $task->room->roomType->name }})</p>
                    </div>
                    <div>
                        <label class="font-medium">Property:</label>
                        <p>{{ $task->property->name }}</p>
                    </div>
                    <div>
                        <label class="font-medium">Task Type:</label>
                        <p>{{ $task->task_type }}</p>
                    </div>
                    <div>
                        <label class="font-medium">Priority:</label>
                        <span class="inline-block px-2 py-1 text-xs rounded-full {{ $task->priority === 'HIGH' ? 'bg-red-100 text-red-800' : ($task->priority === 'MEDIUM' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ $task->priority }}
                        </span>
                    </div>
                    <div>
                        <label class="font-medium">Status:</label>
                        <span class="inline-block px-2 py-1 text-xs rounded-full {{ $task->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : ($task->status === 'IN_PROGRESS' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $task->status }}
                        </span>
                    </div>
                    <div>
                        <label class="font-medium">Scheduled Date:</label>
                        <p>{{ $task->scheduled_date->format('M j, Y') }}</p>
                    </div>
                    @if($task->scheduled_time)
                        <div>
                            <label class="font-medium">Scheduled Time:</label>
                            <p>{{ $task->scheduled_time }}</p>
                        </div>
                    @endif
                    @if($task->started_at)
                        <div>
                            <label class="font-medium">Started At:</label>
                            <p>{{ $task->started_at->format('M j, Y H:i') }}</p>
                        </div>
                    @endif
                    @if($task->completed_at)
                        <div>
                            <label class="font-medium">Completed At:</label>
                            <p>{{ $task->completed_at->format('M j, Y H:i') }}</p>
                        </div>
                    @endif
                    @if($task->progress_percentage)
                        <div>
                            <label class="font-medium">Progress:</label>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $task->progress_percentage }}%"></div>
                            </div>
                            <p class="text-sm text-gray-600">{{ $task->progress_percentage }}%</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Actions</h2>
                <div class="space-y-3">
                    @if($task->status === 'PENDING')
                        <a href="{{ route('tenant.housekeeper.tasks.start', $task) }}" class="block w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-center">Start Task</a>
                    @elseif($task->status === 'IN_PROGRESS')
                        <a href="{{ route('tenant.housekeeper.tasks.complete', $task) }}" class="block w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-center">Complete Task</a>
                        <form method="POST" action="{{ route('tenant.housekeeper.tasks.progress', $task) }}" class="space-y-2">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium">Update Progress (%)</label>
                                <input type="number" name="progress" min="0" max="100" value="{{ $task->progress_percentage ?? 0 }}" class="w-full border rounded px-3 py-2" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Notes</label>
                                <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2">{{ $task->notes }}</textarea>
                            </div>
                            <button type="submit" class="w-full bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update Progress</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('tenant.housekeeper.tasks.room-status', $task) }}" class="space-y-2">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium">Update Room Status</label>
                            <select name="room_status" class="w-full border rounded px-3 py-2" required>
                                <option value="CLEAN" {{ $task->room->status === 'CLEAN' ? 'selected' : '' }}>Clean</option>
                                <option value="DIRTY" {{ $task->room->status === 'DIRTY' ? 'selected' : '' }}>Dirty</option>
                                <option value="OCCUPIED" {{ $task->room->status === 'OCCUPIED' ? 'selected' : '' }}>Occupied</option>
                                <option value="OUT_OF_ORDER" {{ $task->room->status === 'OUT_OF_ORDER' ? 'selected' : '' }}>Out of Order</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">Update Room Status</button>
                    </form>

                    <form method="POST" action="{{ route('tenant.housekeeper.tasks.report-issue', $task) }}" class="space-y-2">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium">Report Issue</label>
                            <select name="issue_type" class="w-full border rounded px-3 py-2" required>
                                <option value="EQUIPMENT_MISSING">Equipment Missing</option>
                                <option value="SUPPLIES_NEEDED">Supplies Needed</option>
                                <option value="ROOM_DAMAGE">Room Damage</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Priority</label>
                            <select name="priority" class="w-full border rounded px-3 py-2" required>
                                <option value="LOW">Low</option>
                                <option value="MEDIUM">Medium</option>
                                <option value="HIGH">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Description</label>
                            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Report Issue</button>
                    </form>
                </div>
            </div>
        </div>

        @if($task->notes)
            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-4">Notes</h2>
                <div class="bg-gray-50 p-4 rounded">
                    <pre class="whitespace-pre-wrap">{{ $task->notes }}</pre>
                </div>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('tenant.housekeeper.tasks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to My Tasks</a>
        </div>
    </div>
</div>
@endsection
