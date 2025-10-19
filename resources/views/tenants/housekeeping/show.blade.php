@extends('layouts.app')

@php
    $pageTitle = str_replace('_', ' ', $task->task_type);
    $breadcrumbs = [
        ['label' => 'Housekeeping', 'url' => route('tenant.housekeeping.index')],
        ['label' => str_replace('_', ' ', $task->task_type)],
    ];
    $statusOptions = ['PENDING','IN_PROGRESS','COMPLETED','VERIFIED','CANCELLED'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-slate-800">{{ str_replace('_', ' ', $task->task_type) }}</h2>
                <x-status-badge :status="$task->status" />
            </div>
            <p class="text-sm text-slate-500">Room {{ $task->room?->number }} • Scheduled {{ $task->scheduled_date?->format('d M Y') }} {{ $task->scheduled_time }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('tenant.housekeeping.assign', $task) }}" class="flex items-center gap-2">
                @csrf
                @method('PUT')
                <select name="assigned_to" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                    <option value="">Unassigned</option>
                    @foreach(($housekeepers ?? []) as $staff)
                        <option value="{{ $staff->id }}" @selected($task->assigned_to == $staff->id)>{{ $staff->full_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Update Assignment</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Task Progress" subtitle="Track real-time progress for this room.">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-slate-600">
                    <div>
                        <p class="text-xs uppercase text-slate-400">Started</p>
                        <p class="font-semibold text-slate-800">{{ $task->started_at?->format('d M Y H:i') ?? 'Not started' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Completed</p>
                        <p class="font-semibold text-slate-800">{{ $task->completed_at?->format('d M Y H:i') ?? 'Pending' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Verified</p>
                        <p class="font-semibold text-slate-800">{{ $task->verified_at?->format('d M Y H:i') ?? 'Awaiting QA' }}</p>
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-xs uppercase text-slate-400 mb-2">Notes</p>
                    <p class="text-sm text-slate-600">{{ $task->notes ?: 'No additional notes provided.' }}</p>
                </div>
            </x-card>

            <x-card title="Status Update" subtitle="Update the current task status.">
                <form method="POST" action="{{ route('tenant.housekeeping.update-status', $task) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                            @foreach($statusOptions as $status)
                                <option value="{{ $status }}" @selected($task->status === $status)>{{ str_replace('_',' ', $status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Add Note</label>
                        <input type="text" name="notes" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" placeholder="Optional note" />
                    </div>
                    <div class="md:col-span-2 flex items-center justify-end gap-3">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Update Status</button>
                    </div>
                </form>
            </x-card>
        </div>
        <div class="space-y-6">
            <x-card title="Room Snapshot">
                <p class="text-sm text-slate-700 font-semibold">Room {{ $task->room?->number }}</p>
                <p class="text-sm text-slate-500">{{ $task->room?->roomType?->name }}</p>
                <p class="text-xs text-slate-400 mt-3">Current status: {{ $task->room?->status }}</p>
            </x-card>
            <x-card title="Audit Trail" subtitle="Historical updates.">
                <ul class="space-y-3 text-sm text-slate-600">
                    @forelse($task->history ?? [] as $entry)
                        <li>
                            <p class="font-semibold text-slate-800">{{ $entry->status }} • {{ $entry->created_at?->diffForHumans() }}</p>
                            <p class="text-xs text-slate-500">{{ $entry->user?->full_name ?? 'System' }} — {{ $entry->notes }}</p>
                        </li>
                    @empty
                        <li class="text-slate-500">No history recorded.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>
@endsection
