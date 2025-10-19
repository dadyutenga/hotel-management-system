@extends('layouts.app')

@php
    $pageTitle = $guest->full_name;
    $breadcrumbs = [
        ['label' => 'Guests', 'url' => route('tenant.guests.index')],
        ['label' => $guest->full_name],
    ];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-2xl font-semibold">
                {{ strtoupper(substr($guest->full_name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-slate-800">{{ $guest->full_name }}</h2>
                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                    <span><i class="fa-solid fa-envelope mr-1"></i>{{ $guest->email }}</span>
                    <span><i class="fa-solid fa-phone mr-1"></i>{{ $guest->phone }}</span>
                    <span><i class="fa-solid fa-flag mr-1"></i>{{ $guest->nationality ?? 'Nationality not set' }}</span>
                    <span><i class="fa-solid fa-id-card mr-1"></i>{{ $guest->id_type }} {{ $guest->id_number }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($guest->trashed())
                <form method="POST" action="{{ route('tenant.guests.destroy', $guest) }}" onsubmit="return confirm('Restore this guest?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="restore" value="1">
                    <button type="submit" class="px-4 py-2 rounded-xl border border-emerald-300 text-emerald-600 text-sm font-semibold hover:bg-emerald-50">
                        <i class="fa-solid fa-rotate-left mr-2"></i>Restore
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('tenant.guests.destroy', $guest) }}" onsubmit="return confirm('Archive this guest?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl border border-rose-200 text-rose-600 text-sm font-semibold hover:bg-rose-50">
                        <i class="fa-solid fa-box-archive mr-2"></i>Archive
                    </button>
                </form>
            @endif
            <a href="{{ route('tenant.guests.edit', $guest) }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                <i class="fa-solid fa-pen mr-2"></i>Edit
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Guest Insights" subtitle="Stay history and engagement metrics.">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">Total Reservations</p>
                        <p class="text-2xl font-semibold text-slate-800">{{ $stats['reservations_count'] ?? $guest->reservations->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">Nights Stayed</p>
                        <p class="text-2xl font-semibold text-slate-800">{{ $stats['nights'] ?? $guest->reservations->sum('nights') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">Lifetime Value</p>
                        <p class="text-2xl font-semibold text-slate-800">{{ number_format($stats['lifetime_value'] ?? $guest->reservations->sum('total_amount'), 2) }}</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <x-status-badge :status="$guest->marketing_consent ? 'Marketing Opt-In' : 'Marketing Opt-Out'" />
                    @if($guest->loyalty_program_info)
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                            <i class="fa-solid fa-crown"></i>Loyalty Member
                        </span>
                    @endif
                    @if($guest->date_of_birth)
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
                            <i class="fa-solid fa-cake-candles"></i>Birthday {{ $guest->date_of_birth->format('d M') }}
                        </span>
                    @endif
                </div>
            </x-card>

            <x-card title="Reservation History" subtitle="Chronological record of guest stays.">
                <x-table :headers="['Reservation', 'Status', 'Stay', 'Room', 'Total', 'Created']" striped>
                    @forelse(($reservations ?? collect()) as $reservation)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4">
                                <a href="{{ route('tenant.reservations.show', $reservation) }}" class="font-semibold text-slate-800 hover:text-amber-600">
                                    {{ $reservation->reference ?? $reservation->id }}
                                </a>
                                <p class="text-xs text-slate-500">Booked by {{ $reservation->creator?->full_name }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <x-status-badge :status="$reservation->status" :map="[
                                    'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'confirmed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'checked_in' => 'bg-sky-100 text-sky-700 border-sky-200',
                                    'checked_out' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'cancelled' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    'no_show' => 'bg-gray-100 text-gray-600 border-gray-200',
                                ]" />
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">
                                <p>{{ $reservation->check_in?->format('d M Y') }} → {{ $reservation->check_out?->format('d M Y') }}</p>
                                <p class="text-xs">{{ $reservation->nights }} nights • {{ $reservation->adults }} adults</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $reservation->room?->number }}
                                <p class="text-xs text-slate-500">{{ $reservation->roomType?->name }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold text-slate-800">
                                {{ number_format($reservation->total_amount ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-4 text-xs text-slate-500">
                                {{ $reservation->created_at?->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-bed-pulse text-3xl mb-4"></i>
                                <p>No reservations found for this guest.</p>
                            </td>
                        </tr>
                    @endforelse
                </x-table>
                @if(isset($reservations) && method_exists($reservations, 'links'))
                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                @endif
            </x-card>
        </div>
        <div class="space-y-6">
            <x-card title="Profile Details">
                <dl class="space-y-4 text-sm text-slate-600">
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Address</dt>
                        <dd>{{ $guest->address ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Preferences</dt>
                        <dd>
                            <pre class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-500 whitespace-pre-wrap">{{ is_array($guest->preferences) ? json_encode($guest->preferences, JSON_PRETTY_PRINT) : ($guest->preferences ?: 'None recorded') }}</pre>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Loyalty Program</dt>
                        <dd>
                            <pre class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-500 whitespace-pre-wrap">{{ is_array($guest->loyalty_program_info) ? json_encode($guest->loyalty_program_info, JSON_PRETTY_PRINT) : ($guest->loyalty_program_info ?: 'Not enrolled') }}</pre>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Notes</dt>
                        <dd>{{ $guest->notes ?: 'No additional notes' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Created</dt>
                        <dd>{{ $guest->created_at?->format('d M Y H:i') }} by {{ $guest->creator?->full_name ?? 'System' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Contact People" subtitle="Linked contacts for the guest.">
                <ul class="space-y-4">
                    @forelse(($guest->contacts ?? []) as $contact)
                        <li class="border border-slate-200 rounded-xl px-4 py-3">
                            <p class="font-semibold text-slate-800">{{ $contact->name }}</p>
                            <p class="text-sm text-slate-500">{{ $contact->relationship }}</p>
                            <div class="mt-2 text-xs text-slate-500 space-y-1">
                                <p><i class="fa-solid fa-phone mr-1"></i>{{ $contact->phone }}</p>
                                <p><i class="fa-solid fa-envelope mr-1"></i>{{ $contact->email }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No additional contacts recorded.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>
@endsection
