@extends('layouts.app')

@section('title', 'My Statistics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">My Statistics ({{ now()->format('F Y') }})</h1>

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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800">Total Tasks</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_tasks'] }}</p>
            </div>

            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800">Completed Tasks</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['completed_tasks'] }}</p>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-800">Pending Tasks</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_tasks'] }}</p>
            </div>

            <div class="bg-purple-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-800">In Progress</h3>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['in_progress_tasks'] }}</p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Performance Metrics</h2>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800">Average Completion Time</h3>
                <p class="text-2xl font-bold text-gray-600">{{ round($stats['average_completion_time']) }} minutes</p>
                <p class="text-sm text-gray-500">For completed tasks this month</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('tenant.housekeeper.tasks.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Back to My Tasks</a>
        </div>
    </div>
</div>
@endsection