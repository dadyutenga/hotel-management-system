@extends('layouts.app')

@php
    $pageTitle = 'Log Maintenance Request';
    $breadcrumbs = [
        ['label' => 'Maintenance', 'url' => route('tenant.maintenance.index')],
        ['label' => 'Create'],
    ];
@endphp

@section('content')
    <form method="POST" action="{{ route('tenant.maintenance.store') }}" class="grid gap-6">
        @csrf
        <x-card title="Issue Details" subtitle="Provide details for the maintenance team.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Room</label>
                    <select name="room_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                        <option value="">Select room</option>
                        @foreach(($rooms ?? []) as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->number }} • {{ $room->roomType?->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Issue Type</label>
                    <input type="text" name="issue_type" value="{{ old('issue_type') }}" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Description</label>
                    <textarea name="description" rows="4" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Priority</label>
                    <select name="priority" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                        @foreach(['LOW','MEDIUM','HIGH','URGENT'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority','MEDIUM') === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Location Details</label>
                    <input type="text" name="location_details" value="{{ old('location_details') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Assign To</label>
                    <select name="assigned_to" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                        <option value="">Unassigned</option>
                        @foreach(($engineers ?? []) as $staff)
                            <option value="{{ $staff->id }}" @selected(old('assigned_to') == $staff->id)>{{ $staff->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Reported At</label>
                    <input type="datetime-local" name="reported_at" value="{{ old('reported_at', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
            </div>
        </x-card>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('tenant.maintenance.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Log Request</button>
        </div>
    </form>
@endsection
