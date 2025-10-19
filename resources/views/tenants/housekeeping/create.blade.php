@extends('layouts.app')

@php
    $pageTitle = 'Create Housekeeping Task';
    $breadcrumbs = [
        ['label' => 'Housekeeping', 'url' => route('tenant.housekeeping.index')],
        ['label' => 'Create'],
    ];
@endphp

@section('content')
    <form method="POST" action="{{ route('tenant.housekeeping.store') }}" class="grid gap-6">
        @csrf
        <x-card title="Task Details" subtitle="Assign work to the housekeeping team.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Room</label>
                    <select name="room_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="">Select room</option>
                        @foreach(($rooms ?? []) as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->number }} • {{ $room->roomType?->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Task Type</label>
                    <select name="task_type" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        @foreach(['DAILY_CLEAN','DEEP_CLEAN','TURNDOWN','INSPECTION','OTHER'] as $type)
                            <option value="{{ $type }}" @selected(old('task_type') === $type)>{{ str_replace('_',' ', $type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Priority</label>
                    <select name="priority" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        @foreach(['LOW','MEDIUM','HIGH'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority','MEDIUM') === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Assign To</label>
                    <select name="assigned_to" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="">Unassigned</option>
                        @foreach(($housekeepers ?? []) as $staff)
                            <option value="{{ $staff->id }}" @selected(old('assigned_to') == $staff->id)>{{ $staff->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Scheduled Date</label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', now()->toDateString()) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Scheduled Time</label>
                    <input type="time" name="scheduled_time" value="{{ old('scheduled_time') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('notes') }}</textarea>
                </div>
            </div>
        </x-card>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('tenant.housekeeping.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600">Create Task</button>
        </div>
    </form>
@endsection
