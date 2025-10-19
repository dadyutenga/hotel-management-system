@extends('layouts.app')

@php
    $pageTitle = 'Occupancy Report';
    $breadcrumbs = [
        ['label' => 'Reports', 'url' => route('tenant.reports.index')],
        ['label' => 'Occupancy'],
    ];
@endphp

@section('content')
    <x-card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('tenant.reports.occupancy') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-sky-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-sky-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Property</label>
                <select name="property" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-sky-200">
                    <option value="">All</option>
                    @foreach(($properties ?? []) as $property)
                        <option value="{{ $property->id }}" @selected(request('property') == $property->id)>{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.reports.occupancy') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card title="Occupancy by Property">
            <canvas id="occupancyChart" height="280"></canvas>
        </x-card>
        <x-card title="Key Metrics" subtitle="Performance indicators">
            <dl class="space-y-4 text-sm text-slate-600">
                <div>
                    <dt class="text-xs uppercase text-slate-400">Average Occupancy</dt>
                    <dd class="text-2xl font-semibold text-slate-800">{{ $metrics['average_occupancy'] ?? '0%' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-400">RevPAR</dt>
                    <dd class="text-2xl font-semibold text-slate-800">{{ number_format($metrics['revpar'] ?? 0, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-400">ADR</dt>
                    <dd class="text-2xl font-semibold text-slate-800">{{ number_format($metrics['adr'] ?? 0, 2) }}</dd>
                </div>
            </dl>
        </x-card>
    </div>

    <div class="mt-6">
        <x-table :headers="['Property', 'Rooms', 'Occupied Nights', 'Occupancy %', 'ADR', 'RevPAR']" striped>
            @foreach(($rows ?? []) as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-sm font-semibold text-slate-800">{{ $row['property'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['rooms'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['occupied_nights'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $row['occupancy'] }}%</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ number_format($row['adr'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ number_format($row['revpar'], 2) }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            const ctx = document.getElementById('occupancyChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chart['labels'] ?? []) !!},
                    datasets: [{
                        label: 'Occupancy %',
                        data: {!! json_encode($chart['data'] ?? []) !!},
                        backgroundColor: '#38bdf8',
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            });
        });
    </script>
@endpush
