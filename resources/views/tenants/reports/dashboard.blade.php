@extends('layouts.app')

@php
    $pageTitle = 'Reports Dashboard';
    $breadcrumbs = [
        ['label' => 'Reports'],
    ];
@endphp

@section('content')
    <div class="grid gap-6 lg:grid-cols-4">
        <x-card title="Occupancy" subtitle="Current occupancy rate" class="lg:col-span-1">
            <p class="text-3xl font-semibold text-slate-800">{{ $metrics['occupancy_rate'] ?? '0%' }}</p>
            <p class="text-sm text-slate-500">{{ $metrics['occupied_rooms'] ?? 0 }} of {{ $metrics['total_rooms'] ?? 0 }} rooms</p>
        </x-card>
        <x-card title="Revenue" subtitle="Month-to-date" class="lg:col-span-1">
            <p class="text-3xl font-semibold text-slate-800">{{ number_format($metrics['revenue_mtd'] ?? 0, 2) }}</p>
            <p class="text-sm text-slate-500">Average daily rate {{ number_format($metrics['adr'] ?? 0, 2) }}</p>
        </x-card>
        <x-card title="Active Guests" subtitle="Currently in-house" class="lg:col-span-1">
            <p class="text-3xl font-semibold text-slate-800">{{ $metrics['active_guests'] ?? 0 }}</p>
            <p class="text-sm text-slate-500">{{ $metrics['arrivals_today'] ?? 0 }} arrivals today</p>
        </x-card>
        <x-card title="Tasks" subtitle="Operational workload" class="lg:col-span-1">
            <p class="text-3xl font-semibold text-slate-800">{{ $metrics['open_tasks'] ?? 0 }}</p>
            <p class="text-sm text-slate-500">{{ $metrics['overdue_tasks'] ?? 0 }} overdue</p>
        </x-card>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mt-6">
        <x-card title="Revenue Trend" subtitle="Last 7 days">
            <canvas id="revenueChart" height="220"></canvas>
        </x-card>
        <x-card title="Reservation Status" subtitle="Current week">
            <canvas id="reservationStatusChart" height="220"></canvas>
        </x-card>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mt-6">
        <x-card title="Top Performing Rooms" subtitle="Based on occupancy">
            <ul class="space-y-3 text-sm text-slate-600">
                @foreach(($topRooms ?? []) as $room)
                    <li class="flex items-center justify-between">
                        <span>{{ $room['room'] }}</span>
                        <span class="font-semibold text-slate-800">{{ $room['occupancy'] }}%</span>
                    </li>
                @endforeach
            </ul>
        </x-card>
        <x-card title="Upcoming Check-ins" subtitle="Next 5 arrivals">
            <ul class="space-y-3 text-sm text-slate-600">
                @foreach(($upcomingArrivals ?? []) as $arrival)
                    <li class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $arrival['guest'] }}</p>
                            <p class="text-xs text-slate-500">{{ $arrival['room'] }}</p>
                        </div>
                        <span>{{ $arrival['date'] }}</span>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($charts['revenue']['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Revenue',
                            data: {!! json_encode($charts['revenue']['data'] ?? []) !!},
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: true,
                        }]
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            const statusCtx = document.getElementById('reservationStatusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($charts['reservations']['labels'] ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($charts['reservations']['data'] ?? []) !!},
                            backgroundColor: ['#22c55e', '#f97316', '#60a5fa', '#f43f5e', '#94a3b8'],
                        }]
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        });
    </script>
@endpush
