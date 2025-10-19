@extends('layouts.app')

@php
    $pageTitle = 'Housekeeping Performance';
    $breadcrumbs = [
        ['label' => 'Reports', 'url' => route('tenant.reports.index')],
        ['label' => 'Housekeeping'],
    ];
@endphp

@section('content')
    <x-card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('tenant.reports.housekeeping') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-lime-400 focus:ring-lime-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-lime-400 focus:ring-lime-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Assigned To</label>
                <select name="assigned" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-lime-400 focus:ring-lime-200">
                    <option value="">All</option>
                    @foreach(($housekeepers ?? []) as $staff)
                        <option value="{{ $staff->id }}" @selected(request('assigned') == $staff->id)>{{ $staff->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.reports.housekeeping') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card title="Tasks by Status">
            <canvas id="tasksStatusChart" height="280"></canvas>
        </x-card>
        <x-card title="Average Completion Time" subtitle="Per priority level">
            <canvas id="completionChart" height="280"></canvas>
        </x-card>
    </div>

    <div class="mt-6">
        <x-table :headers="['Housekeeper', 'Tasks Assigned', 'Completed', 'Avg Time (hrs)', 'Verification Rate']" striped>
            @foreach(($rows ?? []) as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-sm font-semibold text-slate-800">{{ $row['housekeeper'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['assigned'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['completed'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ number_format($row['avg_time'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['verification_rate'] }}%</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            const statusCtx = document.getElementById('tasksStatusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($chart['status']['labels'] ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($chart['status']['data'] ?? []) !!},
                            backgroundColor: ['#22c55e','#f97316','#6366f1','#f43f5e','#94a3b8'],
                        }]
                    },
                    options: { plugins: { legend: { position: 'bottom' } } }
                });
            }

            const completionCtx = document.getElementById('completionChart');
            if (completionCtx) {
                new Chart(completionCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chart['completion']['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Avg Hours',
                            data: {!! json_encode($chart['completion']['data'] ?? []) !!},
                            backgroundColor: '#84cc16',
                        }]
                    },
                    options: { plugins: { legend: { display: false } } }
                });
            }
        });
    </script>
@endpush
