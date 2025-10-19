@extends('layouts.app')

@php
    $pageTitle = 'Guest Insights';
    $breadcrumbs = [
        ['label' => 'Reports', 'url' => route('tenant.reports.index')],
        ['label' => 'Guests'],
    ];
@endphp

@section('content')
    <x-card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('tenant.reports.guests') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Nationality</label>
                <select name="nationality" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    <option value="">All</option>
                    @foreach(($nationalities ?? []) as $country)
                        <option value="{{ $country }}" @selected(request('nationality') == $country)>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.reports.guests') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card title="Guest Demographics" subtitle="By nationality">
            <canvas id="guestNationalityChart" height="280"></canvas>
        </x-card>
        <x-card title="Repeat Guests" subtitle="Loyalty performance">
            <canvas id="repeatGuestsChart" height="280"></canvas>
        </x-card>
    </div>

    <div class="mt-6">
        <x-table :headers="['Guest', 'Nationality', 'Visits', 'Last Stay', 'Marketing Opt-in']" striped>
            @foreach(($guests ?? []) as $guest)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-sm font-semibold text-slate-800">{{ $guest['name'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $guest['nationality'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $guest['visits'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $guest['last_stay'] }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $guest['marketing_consent'] ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </x-table>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            const nationalityCtx = document.getElementById('guestNationalityChart');
            if (nationalityCtx) {
                new Chart(nationalityCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($chart['nationalities']['labels'] ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($chart['nationalities']['data'] ?? []) !!},
                            backgroundColor: ['#0ea5e9','#f97316','#22c55e','#f43f5e','#6366f1','#14b8a6'],
                        }]
                    },
                    options: { plugins: { legend: { position: 'bottom' } } }
                });
            }

            const repeatCtx = document.getElementById('repeatGuestsChart');
            if (repeatCtx) {
                new Chart(repeatCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chart['repeat']['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Guests',
                            data: {!! json_encode($chart['repeat']['data'] ?? []) !!},
                            backgroundColor: '#f59e0b',
                        }]
                    },
                    options: { plugins: { legend: { display: false } } }
                });
            }
        });
    </script>
@endpush
