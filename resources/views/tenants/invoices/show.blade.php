@extends('layouts.app')

@php
    $pageTitle = 'Invoice ' . ($invoice->number ?? $invoice->invoice_number ?? $invoice->id);
    $breadcrumbs = [
        ['label' => 'Invoices', 'url' => route('tenant.invoices.index')],
        ['label' => $invoice->number ?? $invoice->invoice_number ?? $invoice->id],
    ];
    $charges = $invoice->folio?->charges ?? collect();
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Invoice {{ $invoice->number ?? $invoice->invoice_number ?? $invoice->id }}</h2>
            <p class="text-sm text-slate-500">Issued {{ $invoice->issued_at?->format('d M Y') ?? $invoice->created_at?->format('d M Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('tenant.invoices.download', $invoice) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">
                <i class="fa-solid fa-file-arrow-down mr-2"></i>Download PDF
            </a>
            <form method="POST" action="{{ route('tenant.invoices.email', $invoice) }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Email to Guest
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200">
        <div class="bg-slate-900 text-white px-8 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-amber-300">Hotel Invoice</p>
                <h1 class="text-3xl font-semibold mt-1">{{ $invoice->property?->name ?? $invoice->folio?->reservation?->property?->name ?? 'Hotel' }}</h1>
                <p class="text-sm text-slate-300">{{ $invoice->property?->address_line1 ?? $invoice->folio?->reservation?->property?->address_line1 }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm uppercase text-slate-300">Invoice No.</p>
                <p class="text-xl font-semibold">{{ $invoice->number ?? $invoice->invoice_number ?? $invoice->id }}</p>
                <p class="mt-2 text-sm text-slate-300">Due {{ $invoice->due_date?->format('d M Y') ?? 'Upon receipt' }}</p>
            </div>
        </div>
        <div class="px-8 py-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs uppercase text-slate-400">Bill To</p>
                <p class="text-lg font-semibold text-slate-800">{{ $invoice->guest?->full_name ?? $invoice->folio?->guest?->full_name }}</p>
                <p class="text-sm text-slate-500">{{ $invoice->guest?->email ?? $invoice->folio?->guest?->email }}</p>
                <p class="text-sm text-slate-500">{{ $invoice->guest?->phone ?? $invoice->folio?->guest?->phone }}</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-400">Reservation</p>
                <p class="text-lg font-semibold text-slate-800">{{ $invoice->folio?->reservation?->reference }}</p>
                <p class="text-sm text-slate-500">Check-in {{ $invoice->folio?->reservation?->check_in?->format('d M Y') }}</p>
                <p class="text-sm text-slate-500">Check-out {{ $invoice->folio?->reservation?->check_out?->format('d M Y') }}</p>
            </div>
        </div>
        <div class="px-8 pb-8">
            <table class="w-full text-sm text-slate-600">
                <thead class="bg-slate-100 text-slate-500 uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-right">Rate</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($charges as $charge)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-800">{{ $charge->description }}</p>
                                <p class="text-xs text-slate-500">{{ str_replace('_', ' ', $charge->type) }} • {{ $charge->posted_at?->format('d M Y') }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">{{ $charge->quantity ?? 1 }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($charge->rate ?? $charge->amount, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($charge->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500">No charges available for this invoice.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mt-8">
                <div class="text-sm text-slate-600 max-w-sm">
                    <p class="text-xs uppercase text-slate-400 mb-2">Notes</p>
                    <p>{{ $invoice->notes ?: 'Thank you for choosing our hotel. We look forward to welcoming you again.' }}</p>
                </div>
                <div class="w-full md:w-64">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold">{{ number_format($invoice->amount ?? $charges->sum('amount'), 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Tax</span>
                            <span class="font-semibold">{{ number_format($invoice->tax_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Service Charge</span>
                            <span class="font-semibold">{{ number_format($invoice->service_charge ?? 0, 2) }}</span>
                        </div>
                        <div class="border-t border-slate-200 pt-3 flex items-center justify-between text-base font-semibold text-slate-800">
                            <span>Total Due</span>
                            <span>{{ number_format($invoice->total ?? $invoice->total_amount ?? $charges->sum('amount'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 border-t border-slate-200 pt-6 text-xs text-slate-400">
                <p>Payment is due within {{ $invoice->payment_terms ?? '7 days' }}. Late payments may incur additional charges.</p>
                <p class="mt-2">Bank Details: {{ $invoice->property?->bank_account_name ?? 'Hotel Operations' }} • {{ $invoice->property?->bank_account_number ?? '0000000000' }}</p>
            </div>
        </div>
    </div>
@endsection
