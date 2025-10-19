@extends('layouts.app')

@php
    $pageTitle = 'Invoices';
    $breadcrumbs = [
        ['label' => 'Invoices'],
    ];
    $statusOptions = ['ALL', 'PROFORMA', 'ACTUAL', 'VOID'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Invoice Center</h2>
            <p class="text-sm text-slate-500">Review all folio invoices with status tracking.</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('tenant.invoices.index') }}" class="flex items-center gap-2">
                <input type="text" name="number" value="{{ request('number') }}" placeholder="Invoice number" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Search</button>
            </form>
        </div>
    </div>

    <x-card>
        <form method="GET" action="{{ route('tenant.invoices.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach($statusOptions as $option)
                        <option value="{{ $option }}" @selected(request('status', 'ALL') === $option)>{{ str_replace('_', ' ', $option) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Guest</label>
                <x-search-bar name="guest" placeholder="Guest name" :endpoint="route('tenant.guests.search')" value="{{ request('guest') }}" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600">Apply</button>
                <a href="{{ route('tenant.invoices.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="mt-6">
        <x-table :headers="['Invoice', 'Guest', 'Folio', 'Type', 'Total', 'Status', 'Issued']" striped>
            @forelse($invoices as $invoice)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-4 text-sm font-semibold text-slate-800">
                        <a href="{{ route('tenant.invoices.show', $invoice) }}" class="hover:text-amber-600">{{ $invoice->number }}</a>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        <p>{{ $invoice->guest?->full_name }}</p>
                        <p class="text-xs text-slate-500">{{ $invoice->guest?->email }}</p>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">{{ $invoice->folio?->number }}</td>
                    <td class="px-4 py-4 text-sm text-slate-600">{{ $invoice->type }}</td>
                    <td class="px-4 py-4 text-sm text-slate-800 font-semibold">{{ number_format($invoice->total ?? 0, 2) }}</td>
                    <td class="px-4 py-4">
                        <x-status-badge :status="$invoice->status" :map="[
                            'draft' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'sent' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'paid' => 'bg-emerald-200 text-emerald-800 border-emerald-300',
                            'void' => 'bg-rose-100 text-rose-700 border-rose-200'
                        ]" />
                    </td>
                    <td class="px-4 py-4 text-xs text-slate-500">{{ $invoice->issued_at?->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-receipt text-3xl mb-4"></i>
                        <p>No invoices match the selected filters.</p>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>

    <div class="mt-4">
        {{ $invoices->withQueryString()->links() }}
    </div>
@endsection
