@extends('layouts.app')

@php
    $pageTitle = 'Housekeeping';
    $breadcrumbs = [
        ['label' => 'Housekeeping'],
    ];
    $statusOptions = ['ALL', 'PENDING', 'IN_PROGRESS', 'COMPLETED', 'VERIFIED', 'CANCELLED'];
    $priorityOptions = ['ALL', 'LOW', 'MEDIUM', 'HIGH'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Housekeeping Tasks</h2>
            <p class="text-sm text-slate-500">Monitor room readiness and staff productivity.</p>
        </div>
        @can('create', App\Models\HousekeepingTask::class)
            <a href="{{ route('tenant.housekeeping.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-500 text-white text-sm font-semibold shadow hover:bg-amber-600">
                <i class="fa-solid fa-broom"></i>
                New Task
            </a>
        @endcan
    </div>

    <x-card>
        <form method="GET" action="{{ route('tenant.housekeeping.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach($statusOptions as $option)
                        <option value="{{ $option }}" @selected(request('status', 'ALL') === $option)>{{ str_replace('_', ' ', $option) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Priority</label>
                <select name="priority" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach($priorityOptions as $option)
                        <option value="{{ $option }}" @selected(request('priority', 'ALL') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Assigned To</label>
                <select name="assigned" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    <option value="">All</option>
                    @foreach(($housekeepers ?? []) as $staff)
                        <option value="{{ $staff->id }}" @selected(request('assigned') == $staff->id)> {{ $staff->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Scheduled Date</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.housekeeping.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="mt-6">
        <x-table :headers="['Task', 'Room', 'Priority', 'Scheduled', 'Assigned', 'Status', 'Actions']" striped>
            @forelse($tasks as $task)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-4 text-sm text-slate-700">
                        <a href="{{ route('tenant.housekeeping.show', $task) }}" class="font-semibold text-slate-800 hover:text-amber-600">{{ str_replace('_', ' ', $task->task_type) }}</a>
                        <p class="text-xs text-slate-500">Created {{ $task->created_at?->diffForHumans() }}</p>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        {{ $task->room?->number ?? 'N/A' }}
                        <p class="text-xs text-slate-500">{{ $task->room?->roomType?->name }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <x-status-badge :status="$task->priority" :map="[
                            'low' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'medium' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'high' => 'bg-rose-100 text-rose-700 border-rose-200'
                        ]" />
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        {{ $task->scheduled_date?->format('d M Y') }}
                        <p class="text-xs text-slate-500">{{ $task->scheduled_time }}</p>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        {{ $task->assignedTo?->full_name ?? 'Unassigned' }}
                    </td>
                    <td class="px-4 py-4">
                        <form method="POST" action="{{ route('tenant.housekeeping.update-status', $task) }}" class="inline-flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select name="status" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs focus:border-emerald-400 focus:ring-emerald-200" onchange="this.form.submit()">
                                @foreach(['PENDING','IN_PROGRESS','COMPLETED','VERIFIED','CANCELLED'] as $status)
                                    <option value="{{ $status }}" @selected($task->status === $status)>{{ str_replace('_',' ',$status) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="px-4 py-4">
                        <a href="{{ route('tenant.housekeeping.show', $task) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-broom-ball text-3xl mb-4"></i>
                        <p>No housekeeping tasks to display.</p>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>

    <div class="mt-4">
        {{ $tasks->withQueryString()->links() }}
    </div>
@endsection
