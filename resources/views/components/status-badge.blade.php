@props([
    'status',
    'map' => [
        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
        'confirmed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'cancelled' => 'bg-rose-100 text-rose-700 border-rose-200',
        'checked_in' => 'bg-sky-100 text-sky-700 border-sky-200',
        'checked_out' => 'bg-slate-100 text-slate-700 border-slate-200',
        'in_progress' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'overdue' => 'bg-rose-100 text-rose-700 border-rose-200',
    ],
])

@php
    $normalized = str($status)->snake()->value();
    $classes = $map[$normalized] ?? 'bg-slate-100 text-slate-700 border-slate-200';
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-2 px-3 py-1 rounded-full border text-xs font-semibold uppercase tracking-wide', $classes]) }}>
    <i class="fa-solid fa-circle text-[6px]"></i>
    {{ is_string($status) ? str($status)->headline() : $status }}
</span>
