@extends('layouts.app')

@php
    $pageTitle = 'Create Guest';
    $breadcrumbs = [
        ['label' => 'Guests', 'url' => route('tenant.guests.index')],
        ['label' => 'Create'],
    ];
@endphp

@section('content')
    <form method="POST" action="{{ route('tenant.guests.store') }}" class="grid gap-6">
        @csrf
        <x-card title="Guest Information" subtitle="Capture the guest's contact details and preferences.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality') }}" list="nationalities" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                    <datalist id="nationalities">
                        @foreach(($nationalities ?? []) as $nationality)
                            <option value="{{ $nationality }}" />
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">ID Type</label>
                    <select name="id_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="">Select ID type</option>
                        @foreach(['PASSPORT', 'NATIONAL_ID', 'DRIVING_LICENSE', 'OTHER'] as $option)
                            <option value="{{ $option }}" @selected(old('id_type') === $option)>{{ str_replace('_', ' ', $option) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Gender</label>
                    <select name="gender" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                        <option value="">Select gender</option>
                        @foreach(['MALE', 'FEMALE', 'OTHER'] as $gender)
                            <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ str_replace('_', ' ', $gender) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('address') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Preferences</label>
                    <textarea name="preferences" rows="3" placeholder='{"pillow":"soft","floor":"high"}' class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('preferences') }}</textarea>
                    <p class="mt-2 text-xs text-slate-400">JSON format recommended for storing structured preferences.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Loyalty Program Info</label>
                    <textarea name="loyalty_program_info" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('loyalty_program_info') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent')) class="rounded border-slate-300 text-amber-500 focus:ring-amber-200" />
                        <span class="text-sm text-slate-600">Guest agrees to receive marketing communications.</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">{{ old('notes') }}</textarea>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('tenant.guests.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-6 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold shadow hover:bg-amber-600">Create Guest</button>
        </div>
    </form>
@endsection
