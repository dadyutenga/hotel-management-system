@extends('layouts.app')

@php
    $pageTitle = 'Folio ' . ($folio->number ?? $folio->id);
    $breadcrumbs = [
        ['label' => 'Reservations', 'url' => route('tenant.reservations.index')],
    ];
    if ($folio->reservation) {
        $breadcrumbs[] = [
            'label' => 'Reservation ' . ($folio->reservation?->reference ?? $folio->reservation_id),
            'url' => route('tenant.reservations.show', $folio->reservation),
        ];
    }
    $breadcrumbs[] = ['label' => 'Folio'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-slate-800">Folio {{ $folio->number ?? $folio->id }}</h2>
                <x-status-badge :status="$folio->status" :map="[
                    'open' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'closed' => 'bg-slate-100 text-slate-700 border-slate-200',
                ]" />
            </div>
            <p class="text-sm text-slate-500">Guest: {{ $folio->guest?->full_name }} • Reservation: {{ $folio->reservation?->reference ?? $folio->reservation_id }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" @click="$dispatch('open-modal', 'add-charge-modal')" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                <i class="fa-solid fa-plus mr-2"></i>Add Charge
            </button>
            <button type="button" @click="$dispatch('open-modal', 'add-payment-modal')" class="px-4 py-2 rounded-xl border border-emerald-200 text-sm font-semibold text-emerald-600 hover:bg-emerald-50">
                <i class="fa-solid fa-hand-holding-dollar mr-2"></i>Record Payment
            </button>
            @if(!$folio->invoice)
                <button type="button" @click="$dispatch('open-modal', 'generate-invoice-modal')" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">
                    <i class="fa-solid fa-file-invoice mr-2"></i>Generate Invoice
                </button>
            @endif
            @if($folio->status !== 'CLOSED')
                <button type="button" @click="$dispatch('open-modal', 'close-folio-modal')" class="px-4 py-2 rounded-xl bg-rose-500 text-white text-sm font-semibold hover:bg-rose-600">
                    <i class="fa-solid fa-lock mr-2"></i>Close Folio
                </button>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Charges" subtitle="All billable items posted to this folio.">
                <x-table :headers="['Date', 'Description', 'Type', 'Amount', 'Staff']" striped>
                    @forelse($folio->charges ?? [] as $charge)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $charge->posted_at?->format('d M Y H:i') ?? $charge->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700 font-semibold">{{ $charge->description }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ str_replace('_', ' ', $charge->type) }}</td>
                            <td class="px-4 py-4 text-sm text-slate-800 font-semibold">{{ number_format($charge->amount, 2) }}</td>
                            <td class="px-4 py-4 text-xs text-slate-500">{{ $charge->creator?->full_name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">No charges posted yet.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>

            <x-card title="Payments" subtitle="All payments and adjustments applied.">
                <x-table :headers="['Date', 'Method', 'Reference', 'Amount', 'Staff']" striped>
                    @forelse($folio->payments ?? [] as $payment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $payment->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ str_replace('_', ' ', $payment->method) }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $payment->reference ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm text-emerald-600 font-semibold">{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-4 text-xs text-slate-500">{{ $payment->creator?->full_name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">No payments recorded yet.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Balance Summary">
                <dl class="space-y-4 text-sm text-slate-600">
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Total Charges</dt>
                        <dd class="font-semibold text-slate-800">{{ number_format($folio->total_charges ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Total Payments</dt>
                        <dd class="font-semibold text-slate-800">{{ number_format($folio->total_payments ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Taxes & Fees</dt>
                        <dd>{{ number_format($folio->tax_total ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Service Charge</dt>
                        <dd>{{ number_format($folio->service_charge_total ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-slate-400">Balance</dt>
                        <dd class="text-xl font-semibold {{ ($folio->balance ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($folio->balance ?? 0, 2) }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Guest Profile" subtitle="At-a-glance guest summary.">
                <p class="text-sm text-slate-700 font-semibold">{{ $folio->guest?->full_name }}</p>
                <p class="text-sm text-slate-500">{{ $folio->guest?->email }} • {{ $folio->guest?->phone }}</p>
                <p class="text-xs text-slate-400 mt-3">Marketing consent: {{ $folio->guest?->marketing_consent ? 'Yes' : 'No' }}</p>
            </x-card>
        </div>
    </div>

    @push('modals')
        <x-modal id="add-charge-modal" title="Add Charge">
            <form method="POST" action="{{ route('tenant.folios.add-charge', $folio) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Type</label>
                    <select name="type" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200">
                        @foreach(['ROOM', 'F&B', 'SPA', 'OTHER', 'DEPOSIT', 'REFUND'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Description</label>
                    <input type="text" name="description" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Amount</label>
                    <input type="number" step="0.01" min="0" name="amount" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Tax Rate (%)</label>
                    <input type="number" step="0.01" name="tax_rate" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-200" />
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600" @click="$dispatch('close-modal', 'add-charge-modal')">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Add Charge</button>
                </div>
            </form>
        </x-modal>

        <x-modal id="add-payment-modal" title="Record Payment">
            <form method="POST" action="{{ route('tenant.folios.add-payment', $folio) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Method</label>
                    <select name="method" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                        @foreach(['CASH', 'BANK', 'MOBILE', 'CARD', 'ROOM_CHARGE'] as $method)
                            <option value="{{ $method }}">{{ str_replace('_', ' ', $method) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Reference</label>
                    <input type="text" name="reference" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Amount</label>
                    <input type="number" step="0.01" min="0" name="amount" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600" @click="$dispatch('close-modal', 'add-payment-modal')">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Record Payment</button>
                </div>
            </form>
        </x-modal>

        <x-modal id="generate-invoice-modal" title="Generate Invoice">
            <form method="POST" action="{{ route('tenant.folios.generate-invoice', $folio) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Invoice Type</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="PROFORMA">Proforma</option>
                        <option value="ACTUAL">Actual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Email to Guest</label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="email_guest" value="1" class="rounded border-slate-300 text-amber-500 focus:ring-amber-200" />
                        Send a copy to {{ $folio->guest?->email }}
                    </label>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600" @click="$dispatch('close-modal', 'generate-invoice-modal')">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600">Generate</button>
                </div>
            </form>
        </x-modal>

        <x-modal id="close-folio-modal" title="Close Folio">
            <form method="POST" action="{{ route('tenant.folios.close', $folio) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <p class="text-sm text-slate-600">Closing the folio will prevent further charges or payments. Ensure the balance is zero before closing.</p>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600" @click="$dispatch('close-modal', 'close-folio-modal')">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-500 text-white text-sm font-semibold hover:bg-rose-600">Close Folio</button>
                </div>
            </form>
        </x-modal>
    @endpush
@endsection
