@extends('layouts.app')

@php
    $pageTitle = 'Reservation ' . ($reservation->reference ?? $reservation->id);
    $breadcrumbs = [
        ['label' => 'Reservations', 'url' => route('tenant.reservations.index')],
        ['label' => $reservation->reference ?? $reservation->id],
    ];
    $statusMap = [
        'PENDING' => 'bg-amber-100 text-amber-700 border-amber-200',
        'CONFIRMED' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'CHECKED_IN' => 'bg-sky-100 text-sky-700 border-sky-200',
        'CHECKED_OUT' => 'bg-slate-100 text-slate-700 border-slate-200',
        'CANCELLED' => 'bg-rose-100 text-rose-700 border-rose-200',
        'NO_SHOW' => 'bg-gray-100 text-gray-600 border-gray-200',
    ];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-slate-800">Reservation {{ $reservation->reference ?? $reservation->id }}</h2>
                <x-status-badge :status="$reservation->status" :map="$statusMap" />
            </div>
            <p class="text-sm text-slate-500">Created {{ $reservation->created_at?->format('d M Y H:i') }} by {{ $reservation->creator?->full_name ?? 'System' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($reservation->folio)
                <a href="{{ route('tenant.folios.show', $reservation->folio) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">
                    <i class="fa-solid fa-file-invoice mr-2"></i>View Folio
                </a>
            @endif
            @can('update', $reservation)
                <button type="button" @click="$dispatch('open-modal', 'update-status-modal')" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">
                    <i class="fa-solid fa-arrows-rotate mr-2"></i>Update Status
                </button>
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Stay Overview" subtitle="Reservation essentials and stay details.">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-slate-600">
                    <div>
                        <p class="text-xs uppercase text-slate-400">Guest</p>
                        <p class="font-semibold text-slate-800">{{ $reservation->guest?->full_name }}</p>
                        <p>{{ $reservation->guest?->email }}</p>
                        <p>{{ $reservation->guest?->phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Stay Dates</p>
                        <p class="font-semibold text-slate-800">{{ $reservation->check_in?->format('d M Y') }} → {{ $reservation->check_out?->format('d M Y') }}</p>
                        <p>{{ $reservation->nights }} nights • {{ $reservation->adults }} adults, {{ $reservation->children }} children</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Financials</p>
                        <p>Total: <span class="font-semibold text-slate-800">{{ number_format($reservation->total_amount ?? 0, 2) }}</span></p>
                        <p>Paid: {{ number_format($reservation->paid_amount ?? 0, 2) }}</p>
                        <p>Balance: <span class="font-semibold {{ ($reservation->balance_due ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($reservation->balance_due ?? 0, 2) }}</span></p>
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-xs uppercase text-slate-400 mb-2">Special Requests</p>
                    <p class="text-sm text-slate-600">{{ $reservation->special_requests ?: 'No special requests noted.' }}</p>
                </div>
            </x-card>

            <x-card title="Room Assignment" subtitle="Rooms attached to this reservation.">
                <x-table :headers="['Room', 'Type', 'Rate', 'Status']" striped>
                    @forelse($reservation->rooms as $room)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 text-sm text-slate-700 font-semibold">
                                {{ $room->number }}
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $room->roomType?->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ number_format($room->pivot->rate ?? $reservation->nightly_rate ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">
                                <x-status-badge :status="$room->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No rooms assigned yet.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>

            <x-card title="Timeline" subtitle="Status history and staff actions.">
                <ul class="space-y-4">
                    @forelse($reservation->statusHistory ?? [] as $history)
                        <li class="flex items-start gap-3">
                            <span class="w-10 text-xs text-slate-400">{{ $history->created_at?->format('d M') }}</span>
                            <div class="flex-1">
                                <p class="text-sm text-slate-700">
                                    <span class="font-semibold">{{ $history->from_status }} → {{ $history->to_status }}</span>
                                    by {{ $history->user?->full_name ?? 'System' }}
                                </p>
                                @if($history->notes)
                                    <p class="text-xs text-slate-500 mt-1">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No status changes recorded.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
        <div class="space-y-6">
            <x-card title="Folio Summary">
                <dl class="space-y-4 text-sm text-slate-600">
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Folio Number</dt>
                        <dd>{{ $reservation->folio?->number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Charges</dt>
                        <dd>{{ number_format($reservation->folio?->total_charges ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Payments</dt>
                        <dd>{{ number_format($reservation->folio?->total_payments ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Balance</dt>
                        <dd class="font-semibold {{ ($reservation->folio?->balance ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($reservation->folio?->balance ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Invoice</dt>
                        <dd>
                            @if($reservation->folio?->invoice)
                                <a href="{{ route('tenant.invoices.show', $reservation->folio->invoice) }}" class="text-emerald-600 font-semibold hover:text-emerald-700">View invoice</a>
                            @else
                                <span class="text-slate-400">Not generated</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Operations" subtitle="Actions and assignments.">
                <div class="space-y-4 text-sm text-slate-600">
                    <div>
                        <p class="text-xs uppercase text-slate-400">Assigned Staff</p>
                        <p>{{ $reservation->assignedUser?->full_name ?? 'Not assigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Notes</p>
                        <p>{{ $reservation->internal_notes ?: 'No internal notes' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-400">Created</p>
                        <p>{{ $reservation->created_at?->diffForHumans() }}</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    @can('update', $reservation)
        @push('modals')
            <x-modal id="update-status-modal" title="Update Reservation Status">
                <form method="POST" action="{{ route('tenant.reservations.update-status', $reservation) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                            @foreach(array_keys($statusMap) as $status)
                                <option value="{{ $status }}" @selected($reservation->status === $status)>{{ str_replace('_', ' ', $status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600" @click="$dispatch('close-modal', 'update-status-modal')">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Update Status</button>
                    </div>
                </form>
            </x-modal>
        @endpush
    @endcan
@endsection
