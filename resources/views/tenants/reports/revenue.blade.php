@extends('layouts.app')

@php
    $pageTitle = 'Revenue Report';
    $breadcrumbs = [
        ['label' => 'Reports', 'url' => route('tenant.reports.index')],
        ['label' => 'Revenue'],
    ];
@endphp

@section('content')
    <x-card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('tenant.reports.revenue') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Property</label>
                <select name="property" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                    <option value="">All</option>
                    @foreach(($properties ?? []) as $property)
                        <option value="{{ $property->id }}" @selected(request('property') == $property->id)>{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Channel</label>
                <select name="channel" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                    <option value="">All</option>
                    @foreach(($channels ?? []) as $channel)
                        <option value="{{ $channel }}" @selected(request('channel') == $channel)>{{ $channel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.reports.revenue') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card title="Revenue Breakdown" subtitle="By business segment">
            <canvas id="revenueBreakdownChart" height="280"></canvas>
        </x-card>
        <x-card title="ADR vs RevPAR" subtitle="Trend over time">
            <canvas id="adrChart" height="280"></canvas>
        </x-card>
    </div>

    <div class="mt-6">
        <x-table :headers="['Date', 'Room Revenue', 'POS Revenue', 'Other Revenue', 'Total']" striped>
            @foreach(($rows ?? []) as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['date'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ number_format($row['room_revenue'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ number_format($row['pos_revenue'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ number_format($row['other_revenue'], 2) }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-slate-800">{{ number_format($row['total'], 2) }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            const breakdownCtx = document.getElementById('revenueBreakdownChart');
            if (breakdownCtx) {
                new Chart(breakdownCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chart['segments']['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Revenue',
                            data: {!! json_encode($chart['segments']['data'] ?? []) !!},
                            backgroundColor: ['#f97316', '#38bdf8', '#a855f7', '#22c55e'],
                        }]
                    },
                    options: { plugins: { legend: { display: false } } }
                });
            }

            const adrCtx = document.getElementById('adrChart');
            if (adrCtx) {
                new Chart(adrCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chart['trend']['labels'] ?? []) !!},
                        datasets: [
                            {
                                label: 'ADR',
                                data: {!! json_encode($chart['trend']['adr'] ?? []) !!},
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: true,
                            },
                            {
                                label: 'RevPAR',
                                data: {!! json_encode($chart['trend']['revpar'] ?? []) !!},
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true,
                            }
                        ]
                    },
                    options: { plugins: { legend: { position: 'bottom' } } }
                });
            }
        });
    </script>
@endpush
