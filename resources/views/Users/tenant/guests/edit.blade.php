@extends('layouts.app')

@php
    $pageTitle = 'Edit Guest';
    $breadcrumbs = [
        ['label' => 'Guests', 'url' => route('tenant.guests.index')],
        ['label' => $guest->full_name, 'url' => route('tenant.guests.show', $guest)],
        ['label' => 'Edit'],
    ];
@endphp

@section('content')
    <form method="POST" action="{{ route('tenant.guests.update', $guest) }}" class="grid gap-6">
        @csrf
        @method('PUT')
        <x-card title="Guest Profile" subtitle="Update the guest's information and preferences.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $guest->full_name) }}" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $guest->email) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $guest->phone) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', $guest->nationality) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">ID Type</label>
                    <select name="id_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="">Select ID type</option>
                        @foreach(['PASSPORT', 'ID_CARD', 'DRIVERS_LICENSE', 'OTHER'] as $option)
                            <option value="{{ $option }}" @selected(old('id_type', $guest->id_type) === $option)>{{ str_replace('_', ' ', $option) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number', $guest->id_number) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($guest->date_of_birth)->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Gender</label>
                    <select name="gender" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="">Select gender</option>
                        @foreach(['MALE', 'FEMALE', 'OTHER', 'PREFER_NOT_TO_SAY'] as $gender)
                            <option value="{{ $gender }}" @selected(old('gender', $guest->gender) === $gender)>{{ str_replace('_', ' ', $gender) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('address', $guest->address) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Preferences</label>
                    <textarea name="preferences" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('preferences', is_array($guest->preferences) ? json_encode($guest->preferences) : $guest->preferences) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Loyalty Program Info</label>
                    <textarea name="loyalty_program_info" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('loyalty_program_info', is_array($guest->loyalty_program_info) ? json_encode($guest->loyalty_program_info) : $guest->loyalty_program_info) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent', $guest->marketing_consent)) class="rounded border-slate-300 text-amber-500 focus:ring-amber-200" />
                        <span class="text-sm text-slate-600">Guest agrees to receive marketing communications.</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('notes', $guest->notes) }}</textarea>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Last updated {{ $guest->updated_at?->diffForHumans() }} by {{ $guest->updater?->full_name ?? 'System' }}
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.guests.show', $guest) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold shadow hover:bg-amber-600">Update Guest</button>
            </div>
        </div>
    </form>
@endsection
