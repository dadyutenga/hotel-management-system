@extends('layouts.app')

@php
    $pageTitle = 'Maintenance';
    $breadcrumbs = [
        ['label' => 'Maintenance'],
    ];
    $statusOptions = ['ALL','OPEN','ASSIGNED','IN_PROGRESS','ON_HOLD','COMPLETED','CANCELLED'];
    $priorityOptions = ['ALL','LOW','MEDIUM','HIGH','URGENT'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Maintenance Requests</h2>
            <p class="text-sm text-slate-500">Keep the property in top condition with proactive tracking.</p>
        </div>
        @can('create', App\Models\MaintenanceRequest::class)
            <a href="{{ route('tenant.maintenance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                Log Request
            </a>
        @endcan
    </div>

    <x-card>
        <form method="GET" action="{{ route('tenant.maintenance.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                    @foreach($statusOptions as $option)
                        <option value="{{ $option }}" @selected(request('status', 'ALL') === $option)>{{ str_replace('_',' ', $option) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Priority</label>
                <select name="priority" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                    @foreach($priorityOptions as $option)
                        <option value="{{ $option }}" @selected(request('priority','ALL') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Room</label>
                <select name="room" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                    <option value="">All</option>
                    @foreach(($rooms ?? []) as $room)
                        <option value="{{ $room->id }}" @selected(request('room') == $room->id)>{{ $room->number }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Reported From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.maintenance.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="mt-6">
        <x-table :headers="['Issue', 'Location', 'Priority', 'Status', 'Assigned', 'Reported', 'Actions']" striped>
            @forelse($requests as $request)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-4 text-sm text-slate-700">
                        <a href="{{ route('tenant.maintenance.show', $request) }}" class="font-semibold text-slate-800 hover:text-emerald-600">{{ $request->issue_type }}</a>
                        <p class="text-xs text-slate-500">{{ $request->description }}</p>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        Room {{ $request->room?->number ?? 'N/A' }}
                        <p class="text-xs text-slate-500">{{ $request->location_details }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <x-status-badge :status="$request->priority" :map="[
                            'low' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'medium' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'high' => 'bg-rose-100 text-rose-700 border-rose-200',
                            'urgent' => 'bg-red-200 text-red-800 border-red-300'
                        ]" />
                    </td>
                    <td class="px-4 py-4">
                        <x-status-badge :status="$request->status" />
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        {{ $request->assignedTo?->full_name ?? 'Unassigned' }}
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        {{ $request->reported_at?->format('d M Y H:i') }}
                    </td>
                    <td class="px-4 py-4">
                        <a href="{{ route('tenant.maintenance.show', $request) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 text-slate-500 hover:text-emerald-600 hover:border-emerald-300">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-screwdriver-wrench text-3xl mb-4"></i>
                        <p>No maintenance requests found.</p>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>

    <div class="mt-4">
        {{ $requests->withQueryString()->links() }}
    </div>
@endsection
