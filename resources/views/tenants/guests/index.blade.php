@extends('layouts.app')

@php
    $pageTitle = 'Guests';
    $breadcrumbs = [
        ['label' => 'Guests'],
    ];
@endphp

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Guest Directory</h2>
            <p class="text-sm text-slate-500">Manage guest profiles, preferences, and histories.</p>
        </div>
        @can('create', App\Models\Guest::class)
            <a href="{{ route('tenant.guests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold shadow">
                <i class="fa-solid fa-user-plus"></i>
                New Guest
            </a>
        @endcan
    </div>

    <div class="grid gap-6">
        <x-card>
            <form method="GET" action="{{ route('tenant.guests.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <x-search-bar
                        name="query"
                        placeholder="Search guests by name, email, phone or ID"
                        :endpoint="route('tenant.guests.search')"
                        value="{{ request('query') }}"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Nationality</label>
                    <select name="nationality" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="">All</option>
                        @foreach(($nationalities ?? []) as $nationality)
                            <option value="{{ $nationality }}" @selected(request('nationality') === $nationality)>{{ $nationality }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Status</label>
                        <select name="with_trashed" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                            <option value="0" @selected(!request()->boolean('with_trashed'))>Active</option>
                            <option value="1" @selected(request()->boolean('with_trashed'))>Include Archived</option>
                        </select>
                    </div>
                    <button type="submit" class="h-10 px-4 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                        Apply
                    </button>
                </div>
            </form>
        </x-card>

        <x-table :headers="['Guest', 'Contact', 'Nationality', 'Marketing Consent', 'Created', 'Actions']" striped>
            @forelse($guests as $guest)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-4 align-top">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-semibold">
                                {{ strtoupper(substr($guest->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <a href="{{ route('tenant.guests.show', $guest) }}" class="font-semibold text-slate-800 hover:text-amber-600">
                                    {{ $guest->full_name }}
                                </a>
                                <p class="text-xs text-slate-500">ID: {{ $guest->id_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 align-top text-sm text-slate-600">
                        <p><i class="fa-solid fa-envelope mr-2 text-slate-400"></i>{{ $guest->email }}</p>
                        <p><i class="fa-solid fa-phone mr-2 text-slate-400"></i>{{ $guest->phone }}</p>
                    </td>
                    <td class="px-4 py-4 align-top text-sm text-slate-600">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                            <i class="fa-solid fa-flag"></i> {{ $guest->nationality ?? 'Not set' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 align-top">
                        @if($guest->marketing_consent)
                            <x-status-badge status="Opted In" :map="['opted_in' => 'bg-emerald-100 text-emerald-700 border-emerald-200']" />
                        @else
                            <x-status-badge status="Opted Out" :map="['opted_out' => 'bg-slate-100 text-slate-600 border-slate-200']" />
                        @endif
                    </td>
                    <td class="px-4 py-4 align-top text-sm text-slate-500">
                        <p>{{ $guest->created_at?->format('d M Y') }}</p>
                        <p class="text-xs">by {{ $guest->creator?->full_name }}</p>
                    </td>
                    <td class="px-4 py-4 align-top">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('tenant.guests.edit', $guest) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-400">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="{{ route('tenant.guests.show', $guest) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-400">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($guest->trashed())
                                <form method="POST" action="{{ route('tenant.guests.destroy', $guest) }}" onsubmit="return confirm('Restore this guest?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="restore" value="1">
                                    <button type="submit" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-emerald-200 text-emerald-600 hover:bg-emerald-50">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('tenant.guests.destroy', $guest) }}" onsubmit="return confirm('Archive this guest?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-rose-200 text-rose-600 hover:bg-rose-50">
                                        <i class="fa-solid fa-box-archive"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-user-slash text-3xl mb-4"></i>
                        <p>No guests found for the selected filters.</p>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $guests->withQueryString()->links() }}
        </div>
    </div>
@endsection
