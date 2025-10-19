@extends('layouts.app')

@php
    $pageTitle = $request->issue_type;
    $breadcrumbs = [
        ['label' => 'Maintenance', 'url' => route('tenant.maintenance.index')],
        ['label' => $request->issue_type],
    ];
    $statusOptions = ['OPEN','ASSIGNED','IN_PROGRESS','ON_HOLD','COMPLETED','CANCELLED'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-slate-800">{{ $request->issue_type }}</h2>
                <x-status-badge :status="$request->status" />
            </div>
            <p class="text-sm text-slate-500">Reported {{ $request->reported_at?->diffForHumans() }} • Room {{ $request->room?->number ?? 'N/A' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('tenant.maintenance.assign', $request) }}" class="flex items-center gap-2">
                @csrf
                @method('PUT')
                <select name="assigned_to" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                    <option value="">Unassigned</option>
                    @foreach(($engineers ?? []) as $staff)
                        <option value="{{ $staff->id }}" @selected($request->assigned_to == $staff->id)>{{ $staff->full_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Update Assignment</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Issue Overview" subtitle="Detailed report of the maintenance ticket.">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-slate-600">
                    <div>
                        <p class="text-xs uppercase text-slate-400">Location</p>
                        <p class="font-semibold text-slate-800">Room {{ $request->room?->number ?? 'N/A' }}</p>
                        <p>{{ $request->location_details ?: 'General area' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Priority</p>
                        <x-status-badge :status="$request->priority" :map="[
                            'low' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'medium' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'high' => 'bg-rose-100 text-rose-700 border-rose-200',
                            'urgent' => 'bg-red-200 text-red-800 border-red-300'
                        ]" />
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Reported By</p>
                        <p class="font-semibold text-slate-800">{{ $request->reportedBy?->full_name ?? 'System' }}</p>
                        <p>{{ $request->reported_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Assigned Engineer</p>
                        <p class="font-semibold text-slate-800">{{ $request->assignedTo?->full_name ?? 'Unassigned' }}</p>
                        <p>{{ $request->assigned_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-xs uppercase text-slate-400 mb-2">Description</p>
                    <p class="text-sm text-slate-600">{{ $request->description }}</p>
                </div>
            </x-card>

            <x-card title="Status Update">
                <form method="POST" action="{{ route('tenant.maintenance.update-status', $request) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                            @foreach($statusOptions as $status)
                                <option value="{{ $status }}" @selected($request->status === $status)>{{ str_replace('_',' ', $status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Resolution Notes</label>
                        <textarea name="resolution_notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">{{ old('resolution_notes', $request->resolution_notes) }}</textarea>
                    </div>
                    <div class="md:col-span-2 flex items-center justify-end gap-3">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Update Status</button>
                    </div>
                </form>
            </x-card>
        </div>
        <div class="space-y-6">
            <x-card title="Timeline">
                <dl class="space-y-4 text-sm text-slate-600">
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Started</dt>
                        <dd>{{ $request->started_at?->format('d M Y H:i') ?? 'Not started' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Completed</dt>
                        <dd>{{ $request->completed_at?->format('d M Y H:i') ?? 'Pending' }}</dd>
                    </div>
                </dl>
            </x-card>
            <x-card title="Attachments" subtitle="Upload via maintenance portal.">
                <p class="text-sm text-slate-500">No attachments uploaded.</p>
            </x-card>
        </div>
    </div>
@endsection
