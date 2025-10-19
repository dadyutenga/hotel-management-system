@extends('layouts.app')

@php
    $pageTitle = 'POS Orders';
    $breadcrumbs = [
        ['label' => 'POS'],
    ];
    $statusOptions = ['ALL','OPEN','COMPLETED','CANCELLED'];
    $typeOptions = ['ALL','DINE_IN','TAKE_AWAY','ROOM_SERVICE'];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Point of Sale</h2>
            <p class="text-sm text-slate-500">Manage restaurant, bar, and room service orders.</p>
        </div>
        @can('create', App\Models\PosOrder::class)
            <a href="{{ route('tenant.pos.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600">
                <i class="fa-solid fa-cash-register"></i>
                New Order
            </a>
        @endcan
    </div>

    <x-card>
        <form method="GET" action="{{ route('tenant.pos.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach($statusOptions as $option)
                        <option value="{{ $option }}" @selected(request('status','ALL') === $option)>{{ str_replace('_',' ', $option) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Type</label>
                <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach($typeOptions as $option)
                        <option value="{{ $option }}" @selected(request('type','ALL') === $option)>{{ str_replace('_',' ', $option) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Server</label>
                <select name="server" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    <option value="">All</option>
                    @foreach(($servers ?? []) as $staff)
                        <option value="{{ $staff->id }}" @selected(request('server') == $staff->id)>{{ $staff->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Apply</button>
                <a href="{{ route('tenant.pos.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Reset</a>
            </div>
        </form>
    </x-card>

    <div class="mt-6">
        <x-table :headers="['Order', 'Type', 'Guest Count', 'Total', 'Status', 'Server', 'Actions']" striped>
            @forelse($orders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-4 text-sm text-slate-700">
                        <a href="{{ route('tenant.pos.show', $order) }}" class="font-semibold text-slate-800 hover:text-amber-600">{{ $order->order_number }}</a>
                        <p class="text-xs text-slate-500">{{ $order->created_at?->format('d M Y H:i') }}</p>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">{{ str_replace('_',' ', $order->order_type) }}</td>
                    <td class="px-4 py-4 text-sm text-slate-600">{{ $order->guest_count }}</td>
                    <td class="px-4 py-4 text-sm text-slate-800 font-semibold">{{ number_format($order->total_amount ?? 0, 2) }}</td>
                    <td class="px-4 py-4">
                        <x-status-badge :status="$order->status" :map="[
                            'open' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'cancelled' => 'bg-rose-100 text-rose-700 border-rose-200'
                        ]" />
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">{{ $order->server?->full_name ?? 'N/A' }}</td>
                    <td class="px-4 py-4">
                        <a href="{{ route('tenant.pos.show', $order) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-receipt text-3xl mb-4"></i>
                        <p>No POS orders found.</p>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>

    <div class="mt-4">
        {{ $orders->withQueryString()->links() }}
    </div>
@endsection
