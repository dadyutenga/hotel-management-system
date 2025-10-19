@extends('layouts.app')

@php
    $pageTitle = 'Reservations';
    $breadcrumbs = [
        ['label' => 'Reservations'],
    ];
    $statusOptions = ['ALL', 'PENDING', 'CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT', 'CANCELLED', 'NO_SHOW'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Reservation Center</h2>
            <p class="text-sm text-slate-500">Track all bookings, availability, and guest arrivals.</p>
        </div>
        @can('create', App\Models\Reservation::class)
            <a href="{{ route('tenant.reservations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold shadow">
                <i class="fa-solid fa-plus"></i>
                New Reservation
            </a>
        @endcan
    </div>

    <div class="grid gap-6">
        <x-card>
            <form method="GET" action="{{ route('tenant.reservations.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Guest</label>
                    <x-search-bar
                        name="guest"
                        placeholder="Search guest name or email"
                        :endpoint="route('tenant.guests.search')"
                        value="{{ request('guest') }}"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option }}" @selected(request('status', 'ALL') === $option)>{{ str_replace('_', ' ', $option) }}</option>
                        @endforeach
                    </select>
                </div>
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
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Filter</button>
                    <a href="{{ route('tenant.reservations.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
                </div>
            </form>
        </x-card>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <x-card title="Arrivals Today" class="xl:col-span-1">
                <p class="text-3xl font-semibold text-slate-800">{{ $metrics['arrivals_today'] ?? 0 }}</p>
                <p class="text-sm text-slate-500">Guests scheduled to check-in today.</p>
            </x-card>
            <x-card title="Departures" class="xl:col-span-1">
                <p class="text-3xl font-semibold text-slate-800">{{ $metrics['departures_today'] ?? 0 }}</p>
                <p class="text-sm text-slate-500">Guests checking out today.</p>
            </x-card>
            <x-card title="In-House" class="xl:col-span-1">
                <p class="text-3xl font-semibold text-slate-800">{{ $metrics['in_house'] ?? 0 }}</p>
                <p class="text-sm text-slate-500">Current guests staying in-house.</p>
            </x-card>
            <x-card title="Occupancy" class="xl:col-span-1">
                <p class="text-3xl font-semibold text-slate-800">{{ $metrics['occupancy_rate'] ?? '0%' }}</p>
                <p class="text-sm text-slate-500">Based on confirmed reservations.</p>
            </x-card>
        </div>

        <x-table :headers="['Reservation', 'Guest', 'Stay', 'Rooms', 'Status', 'Balance', 'Actions']" striped>
            @forelse($reservations as $reservation)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-4 align-top">
                        <a href="{{ route('tenant.reservations.show', $reservation) }}" class="font-semibold text-slate-800 hover:text-emerald-600">
                            {{ $reservation->reference ?? $reservation->id }}
                        </a>
                        <p class="text-xs text-slate-500">Source: {{ $reservation->source ?? 'N/A' }}</p>
                    </td>
                    <td class="px-4 py-4 align-top text-sm text-slate-600">
                        <p>{{ $reservation->guest?->full_name }}</p>
                        <p class="text-xs text-slate-500">{{ $reservation->guest?->email }}</p>
                    </td>
                    <td class="px-4 py-4 align-top text-sm text-slate-600">
                        <p>{{ $reservation->check_in?->format('d M Y') }} → {{ $reservation->check_out?->format('d M Y') }}</p>
                        <p class="text-xs">{{ $reservation->nights }} nights • {{ $reservation->adults }} adults</p>
                    </td>
                    <td class="px-4 py-4 align-top text-sm text-slate-600">
                        <p>{{ $reservation->rooms_count ?? $reservation->rooms?->count() }} rooms</p>
                        <p class="text-xs text-slate-500">{{ collect($reservation->rooms ?? [])->pluck('number')->implode(', ') }}</p>
                    </td>
                    <td class="px-4 py-4 align-top">
                        <x-status-badge :status="$reservation->status" />
                    </td>
                    <td class="px-4 py-4 align-top text-sm font-semibold text-slate-800">
                        {{ number_format($reservation->balance_due ?? 0, 2) }}
                    </td>
                    <td class="px-4 py-4 align-top">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('tenant.reservations.show', $reservation) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 text-slate-500 hover:text-emerald-600 hover:border-emerald-300">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @can('update', $reservation)
                                <a href="{{ route('tenant.reservations.show', $reservation) }}#status" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 text-slate-500 hover:text-emerald-600 hover:border-emerald-300">
                                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                </a>
                            @endcan
                            @can('update', $reservation)
                                <form method="POST" action="{{ route('tenant.reservations.update-status', $reservation) }}" class="hidden">
                                    @csrf
                                    @method('PUT')
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-calendar-xmark text-3xl mb-4"></i>
                        <p>No reservations match the current filters.</p>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $reservations->withQueryString()->links() }}
        </div>
    </div>
@endsection
