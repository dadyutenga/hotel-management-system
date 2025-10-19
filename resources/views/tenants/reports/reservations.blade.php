@extends('layouts.app')

@php
    $pageTitle = 'Reservation Analytics';
    $breadcrumbs = [
        ['label' => 'Reports', 'url' => route('tenant.reports.index')],
        ['label' => 'Reservations'],
    ];
@endphp

@section('content')
    <x-card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('tenant.reports.reservations') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Source</label>
                <select name="source" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200">
                    <option value="">All</option>
                    @foreach(($sources ?? []) as $source)
                        <option value="{{ $source }}" @selected(request('source') == $source)>{{ $source }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200">
                    <option value="">All</option>
                    @foreach(['PENDING','CONFIRMED','CHECKED_IN','CHECKED_OUT','CANCELLED','NO_SHOW'] as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>{{ str_replace('_',' ', $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.reports.reservations') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card title="Reservation Trends" subtitle="Daily pick-up">
            <canvas id="reservationTrendChart" height="280"></canvas>
        </x-card>
        <x-card title="Cancellation vs No-show" subtitle="Performance ratios">
            <canvas id="cancellationChart" height="280"></canvas>
        </x-card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 mt-6">
        <x-card title="Average Length of Stay">
            <p class="text-3xl font-semibold text-slate-800">{{ number_format($metrics['los'] ?? 0, 1) }} nights</p>
        </x-card>
        <x-card title="Conversion Rate">
            <p class="text-3xl font-semibold text-slate-800">{{ $metrics['conversion_rate'] ?? '0%' }}</p>
        </x-card>
        <x-card title="Cancellation Rate">
            <p class="text-3xl font-semibold text-slate-800">{{ $metrics['cancellation_rate'] ?? '0%' }}</p>
        </x-card>
    </div>

    <div class="mt-6">
        <x-table :headers="['Date', 'Reservations', 'Cancellations', 'No Shows', 'Conversion %']" striped>
            @foreach(($rows ?? []) as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['date'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['reservations'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['cancellations'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['no_shows'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['conversion'] }}%</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            const trendCtx = document.getElementById('reservationTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chart['trend']['labels'] ?? []),
                        datasets: [{
                            label: 'Reservations',
                            data: @json($chart['trend']['data'] ?? []),
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.1)',
                            tension: 0.4,
                            fill: true,
                        }]
                    },
                    options: { plugins: { legend: { display: false } } }
                });
            }

            const cancellationCtx = document.getElementById('cancellationChart');
            if (cancellationCtx) {
                new Chart(cancellationCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chart['cancellation']['labels'] ?? []),
                        datasets: [{
                            label: 'Cancellations',
                            data: @json($chart['cancellation']['cancelled'] ?? []),
                            backgroundColor: '#f97316',
                        }, {
                            label: 'No Shows',
                            data: @json($chart['cancellation']['no_show'] ?? []),
                            backgroundColor: '#f43f5e',
                        }]
                    },
                    options: { plugins: { legend: { position: 'bottom' } } }
                });
            }
        });
    </script>
@endpush
