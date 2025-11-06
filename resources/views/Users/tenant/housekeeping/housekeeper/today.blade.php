@extends('layouts.app')

@section('title', 'Today\'s Tasks')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Today's Tasks</h1>

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

        @if($tasks->count() > 0)
            <div class="space-y-4">
                @foreach($tasks as $task)
                    <div class="border rounded-lg p-4 {{ $task->status === 'COMPLETED' ? 'bg-green-50' : ($task->status === 'IN_PROGRESS' ? 'bg-blue-50' : 'bg-white') }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold">{{ $task->room->room_number }} - {{ $task->task_type }}</h3>
                                <p class="text-sm text-gray-600">{{ $task->property->name }}</p>
                                <p class="text-sm">{{ $task->notes }}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full {{ $task->priority === 'HIGH' ? 'bg-red-100 text-red-800' : ($task->priority === 'MEDIUM' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $task->priority }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 text-xs rounded-full {{ $task->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : ($task->status === 'IN_PROGRESS' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $task->status }}
                                </span>
                                @if($task->status === 'PENDING')
                                    <a href="{{ route('tenant.housekeeper.tasks.start', $task) }}" class="block mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">Start Task</a>
                                @elseif($task->status === 'IN_PROGRESS')
                                    <a href="{{ route('tenant.housekeeper.tasks.complete', $task) }}" class="block mt-2 bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">Complete Task</a>
                                @endif
                                <a href="{{ route('tenant.housekeeper.tasks.show', $task) }}" class="block mt-1 text-blue-500 hover:text-blue-700 text-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No tasks scheduled for today.</p>
        @endif
    </div>
</div>
@endsection