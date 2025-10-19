@extends('layouts.app')

@php
    $pageTitle = 'POS Order ' . $order->order_number;
    $breadcrumbs = [
        ['label' => 'POS', 'url' => route('tenant.pos.index')],
        ['label' => $order->order_number],
    ];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-slate-800">Order {{ $order->order_number }}</h2>
                <x-status-badge :status="$order->status" />
            </div>
            <p class="text-sm text-slate-500">{{ str_replace('_',' ', $order->order_type) }} • {{ $order->created_at?->format('d M Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('tenant.pos.process-payment', $order) }}" class="flex items-center gap-2">
                @csrf
                <input type="number" step="0.01" min="0" name="amount" placeholder="Amount" class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                <select name="method" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach(['CASH','CARD','MOBILE','ROOM_CHARGE'] as $method)
                        <option value="{{ $method }}">{{ str_replace('_',' ', $method) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600">Record Payment</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Order Items">
                <x-table :headers="['Item', 'Qty', 'Unit Price', 'Total']" striped>
                    @forelse($order->posOrderItems ?? [] as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $item->menuItem?->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $item->quantity }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ number_format($item->unit_price ?? $item->price ?? 0, 2) }}</td>
                            <td class="px-4 py-4 text-sm text-slate-800 font-semibold">{{ number_format($item->total ?? ($item->quantity * ($item->unit_price ?? 0)), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">No items recorded for this order.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>

            <x-card title="Payments">
                <x-table :headers="['Date', 'Method', 'Amount', 'Reference']" striped>
                    @forelse($order->posPayments ?? [] as $payment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $payment->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ str_replace('_',' ', $payment->method) }}</td>
                            <td class="px-4 py-4 text-sm text-emerald-600 font-semibold">{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-4 text-sm text-slate-500">{{ $payment->reference ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">No payments recorded yet.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>
        <div class="space-y-6">
            <x-card title="Summary">
                <dl class="space-y-3 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <dt>Subtotal</dt>
                        <dd>{{ number_format($order->subtotal ?? 0, 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Tax</dt>
                        <dd>{{ number_format($order->tax_amount ?? 0, 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Service Charge</dt>
                        <dd>{{ number_format($order->service_charge ?? 0, 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Discount</dt>
                        <dd>{{ number_format($order->discount_amount ?? 0, 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between font-semibold text-base text-slate-800">
                        <dt>Total</dt>
                        <dd>{{ number_format($order->total_amount ?? 0, 2) }}</dd>
                    </div>
                </dl>
                @if($order->order_type === 'ROOM_SERVICE' && $order->folio)
                    <a href="{{ route('tenant.folios.show', $order->folio) }}" class="mt-4 inline-flex items-center gap-2 text-sm text-emerald-600 font-semibold hover:text-emerald-700">
                        <i class="fa-solid fa-file-invoice"></i> View linked folio
                    </a>
                @endif
            </x-card>

            <x-card title="Order Notes">
                <p class="text-sm text-slate-600">{{ $order->notes ?: 'No additional notes.' }}</p>
            </x-card>
        </div>
    </div>
@endsection
