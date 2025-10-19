@extends('layouts.app')

@php
    $pageTitle = 'Create Reservation';
    $breadcrumbs = [
        ['label' => 'Reservations', 'url' => route('tenant.reservations.index')],
        ['label' => 'Create'],
    ];
@endphp

@section('content')
    <form method="POST" action="{{ route('tenant.reservations.store') }}" x-data="reservationForm({
        availableEndpoint: '{{ route('tenant.reservations.available-rooms') }}',
        defaultCurrency: '{{ $currency ?? 'USD' }}',
        taxes: @json($taxes ?? []),
        ratePlans: @json($ratePlans ?? [])
    })" x-on:search-selected.window="handleGuestSelection($event)" x-on:search-cleared.window="clearGuestSelection($event)" class="grid gap-6">
        @csrf
        <x-card title="Guest & Stay Details" subtitle="Select guest, dates, and stay preferences.">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Guest</label>
                    <x-search-bar name="guest_query" placeholder="Search by name, email or phone" :endpoint="route('tenant.guests.search')" />
                    <input type="hidden" name="guest_id" x-model="form.guest_id">
                    <p class="mt-2 text-xs text-slate-500" x-show="form.guest_name">Selected: <span class="font-semibold" x-text="form.guest_name"></span></p>
                    <a href="{{ route('tenant.guests.create') }}" class="inline-flex items-center gap-2 mt-3 text-sm text-emerald-600 font-semibold hover:text-emerald-700">
                        <i class="fa-solid fa-user-plus"></i> Create new guest
                    </a>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Check-in</label>
                    <input type="date" name="check_in" x-model="form.check_in" x-on:change="calculateNights" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Check-out</label>
                    <input type="date" name="check_out" x-model="form.check_out" x-on:change="calculateNights" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Adults</label>
                    <input type="number" min="1" name="adults" x-model="form.adults" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Children</label>
                    <input type="number" min="0" name="children" x-model="form.children" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Rate Plan</label>
                    <select name="rate_plan_id" x-model="form.rate_plan_id" x-on:change="updateRatePlan" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                        <option value="">Select plan</option>
                        <template x-for="plan in ratePlans" :key="plan.id">
                            <option :value="plan.id" x-text="plan.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Room Type</label>
                    <select name="room_type_id" x-model="form.room_type_id" x-on:change="fetchAvailableRooms" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">
                        <option value="">Select type</option>
                        @foreach(($roomTypes ?? []) as $roomType)
                            <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Available Rooms</label>
                    <div class="space-y-3 max-h-48 overflow-y-auto border border-slate-200 rounded-xl p-3">
                        <template x-if="!availableRooms.length">
                            <p class="text-xs text-slate-500">Select dates and room type to load available rooms.</p>
                        </template>
                        <template x-for="room in availableRooms" :key="room.id">
                            <label class="flex items-center gap-3 text-sm text-slate-600">
                                <input type="checkbox" name="room_ids[]" :value="room.id" x-model="form.room_ids" class="rounded border-slate-300 text-emerald-500 focus:ring-emerald-200" />
                                <div>
                                    <p class="font-semibold text-slate-800" x-text="room.label"></p>
                                    <p class="text-xs text-slate-500" x-text="room.description"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                    <p class="mt-2 text-xs text-emerald-600" x-show="form.room_ids.length">{{ __('Selected rooms: ') }}<span x-text="form.room_ids.length"></span></p>
                </div>
            </div>
        </x-card>

        <x-card title="Charges & Preferences" subtitle="Review nightly rates and add-on options.">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Nightly Rate</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-xs text-slate-400" x-text="form.currency"></span>
                        <input type="number" step="0.01" name="nightly_rate" x-model="form.nightly_rate" x-on:input="calculateTotals" class="w-full pl-10 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Discount (%)</label>
                    <input type="number" step="0.01" name="discount_percent" x-model="form.discount_percent" x-on:input="calculateTotals" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Deposit</label>
                    <input type="number" step="0.01" name="deposit_amount" x-model="form.deposit_amount" x-on:input="calculateTotals" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200" />
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Special Requests</label>
                    <textarea name="special_requests" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-emerald-400 focus:ring-emerald-200">{{ old('special_requests') }}</textarea>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-slate-600">
                <div class="p-4 rounded-xl bg-slate-50">
                    <p class="text-xs uppercase text-slate-400">Nights</p>
                    <p class="text-xl font-semibold text-slate-800" x-text="form.nights"></p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50">
                    <p class="text-xs uppercase text-slate-400">Room Count</p>
                    <p class="text-xl font-semibold text-slate-800" x-text="form.room_ids.length"></p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50">
                    <p class="text-xs uppercase text-slate-400">Sub Total</p>
                    <p class="text-xl font-semibold text-slate-800">
                        <span x-text="formattedAmount(form.subtotal)"></span>
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50">
                    <p class="text-xs uppercase text-slate-400">Grand Total</p>
                    <p class="text-xl font-semibold text-slate-800">
                        <span x-text="formattedAmount(form.total)"></span>
                    </p>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-between">
            <a href="{{ route('tenant.reservations.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-500 text-white text-sm font-semibold shadow hover:bg-emerald-600">Create Reservation</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function reservationForm({ availableEndpoint, defaultCurrency, taxes, ratePlans }) {
            return {
                availableEndpoint,
                ratePlans,
                taxes,
                form: {
                    guest_id: null,
                    guest_name: null,
                    check_in: '',
                    check_out: '',
                    nights: 0,
                    adults: 1,
                    children: 0,
                    room_type_id: '',
                    room_ids: [],
                    rate_plan_id: '',
                    nightly_rate: 0,
                    discount_percent: 0,
                    deposit_amount: 0,
                    currency: defaultCurrency,
                    subtotal: 0,
                    total: 0,
                },
                availableRooms: [],
                handleGuestSelection(event) {
                    if (event.detail.name !== 'guest_query') return;
                    const value = event.detail.value;
                    this.form.guest_id = value.id;
                    this.form.guest_name = value.label;
                },
                clearGuestSelection(event) {
                    if (event.detail.name !== 'guest_query') return;
                    this.form.guest_id = null;
                    this.form.guest_name = null;
                },
                calculateNights() {
                    if (!this.form.check_in || !this.form.check_out) {
                        this.form.nights = 0;
                        return;
                    }
                    const checkIn = new Date(this.form.check_in);
                    const checkOut = new Date(this.form.check_out);
                    const diffTime = checkOut - checkIn;
                    this.form.nights = diffTime > 0 ? Math.ceil(diffTime / (1000 * 60 * 60 * 24)) : 0;
                    if (this.form.nights <= 0) {
                        this.form.nights = 0;
                    }
                    this.fetchAvailableRooms();
                    this.calculateTotals();
                },
                updateRatePlan() {
                    const plan = this.ratePlans.find(plan => plan.id == this.form.rate_plan_id);
                    if (plan) {
                        this.form.nightly_rate = plan.nightly_rate;
                        this.form.currency = plan.currency || defaultCurrency;
                        this.calculateTotals();
                    }
                },
                fetchAvailableRooms() {
                    if (!this.form.room_type_id || !this.form.check_in || !this.form.check_out) {
                        this.availableRooms = [];
                        return;
                    }
                    const params = new URLSearchParams({
                        room_type_id: this.form.room_type_id,
                        check_in: this.form.check_in,
                        check_out: this.form.check_out,
                    });
                    fetch(`${this.availableEndpoint}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    })
                        .then(response => response.json())
                        .then(data => {
                            this.availableRooms = data.rooms || [];
                        });
                },
                calculateTotals() {
                    const nights = this.form.nights || 0;
                    const roomCount = this.form.room_ids.length || 1;
                    const base = parseFloat(this.form.nightly_rate || 0) * nights * roomCount;
                    const discount = base * (parseFloat(this.form.discount_percent || 0) / 100);
                    const subtotal = base - discount;
                    const taxTotal = (this.taxes || []).reduce((acc, tax) => acc + subtotal * (tax.rate / 100), 0);
                    this.form.subtotal = subtotal;
                    this.form.total = subtotal + taxTotal - parseFloat(this.form.deposit_amount || 0);
                },
                formattedAmount(value) {
                    const number = Number(value || 0).toFixed(2);
                    return `${this.form.currency} ${number}`;
                }
            }
        }
    </script>
@endpush
