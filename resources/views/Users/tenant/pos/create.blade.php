@extends('layouts.app')

@php
    $pageTitle = 'Create POS Order';
    $breadcrumbs = [
        ['label' => 'POS', 'url' => route('tenant.pos.index')],
        ['label' => 'Create'],
    ];
@endphp

@section('content')
    <form method="POST" action="{{ route('tenant.pos.store') }}" x-data="posOrderBuilder({
        menuItems: @json($menuItems ?? []),
        rooms: @json($rooms ?? []),
        defaultType: '{{ old('order_type', 'DINE_IN') }}'
    })" class="grid gap-6">
        @csrf
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Order Details" subtitle="Select type, table, and guest count.">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Order Type</label>
                            <select name="order_type" x-model="form.order_type" x-on:change="handleTypeChange" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                                @foreach(['DINE_IN','TAKE_AWAY','ROOM_SERVICE'] as $type)
                                    <option value="{{ $type }}">{{ str_replace('_',' ', $type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Guest Count</label>
                            <input type="number" min="1" name="guest_count" x-model="form.guest_count" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                        </div>
                        <div x-show="form.order_type === 'DINE_IN'" x-cloak>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Table Number</label>
                            <input type="text" name="table_number" x-model="form.table_number" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                        </div>
                        <div x-show="form.order_type === 'ROOM_SERVICE'" class="md:col-span-2" x-cloak>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Room</label>
                            <select name="room_id" x-model="form.room_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                                <option value="">Select room</option>
                                <template x-for="room in rooms" :key="room.id">
                                    <option :value="room.id" x-text="room.label ?? (`Room ${room.number}`)"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </x-card>

                <x-card title="Menu Items" subtitle="Tap to add items to the order.">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="item in menuItems" :key="item.id">
                            <button type="button" class="border border-slate-200 rounded-xl px-4 py-3 text-left hover:border-amber-400 hover:bg-amber-50"
                                @click="addItem(item)">
                                <p class="font-semibold text-slate-800" x-text="item.name"></p>
                                <p class="text-xs text-slate-500" x-text="item.category"></p>
                                <p class="text-sm text-amber-600 font-semibold mt-2" x-text="currency(item.price)"></p>
                            </button>
                        </template>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Order Summary" subtitle="Review items and totals.">
                    <div class="space-y-4">
                        <template x-if="!form.items.length">
                            <p class="text-sm text-slate-500">No items added yet.</p>
                        </template>
                        <template x-for="(item, index) in form.items" :key="item.uid">
                            <div class="flex items-start gap-3 border border-slate-200 rounded-xl px-4 py-3">
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-800" x-text="item.name"></p>
                                    <p class="text-xs text-slate-500" x-text="item.category"></p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <button type="button" class="h-6 w-6 rounded-full border border-slate-200 text-slate-500" @click="decrement(index)"><i class="fa-solid fa-minus text-xs"></i></button>
                                        <input type="number" min="1" x-model.number="item.quantity" @input="recalculate" class="w-12 text-center rounded border border-slate-200 text-sm" />
                                        <button type="button" class="h-6 w-6 rounded-full border border-slate-200 text-slate-500" @click="increment(index)"><i class="fa-solid fa-plus text-xs"></i></button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-slate-600" x-text="currency(item.price)"></p>
                                    <p class="text-sm font-semibold text-slate-800" x-text="currency(item.total)"></p>
                                    <button type="button" class="text-xs text-rose-500 mt-2" @click="remove(index)">Remove</button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="mt-6 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span x-text="currency(form.subtotal)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Tax</span>
                            <span x-text="currency(form.tax_amount)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Service Charge</span>
                            <span x-text="currency(form.service_charge)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Discount</span>
                            <input type="number" step="0.01" min="0" name="discount_amount" x-model="form.discount_amount" @input="recalculate" class="w-24 rounded border border-slate-200 text-sm text-right px-2 py-1" />
                        </div>
                        <div class="border-t border-slate-200 pt-3 flex items-center justify-between text-base font-semibold text-slate-800">
                            <span>Total</span>
                            <span x-text="currency(form.total_amount)"></span>
                        </div>
                    </div>
                    <template x-for="item in form.items" :key="'hidden-' + item.uid">
                        <div>
                            <input type="hidden" name="items[][menu_item_id]" :value="item.id" />
                            <input type="hidden" name="items[][quantity]" :value="item.quantity" />
                            <input type="hidden" name="items[][price]" :value="item.price" />
                            <input type="hidden" name="items[][total]" :value="item.total" />
                        </div>
                    </template>
                    <input type="hidden" name="subtotal" :value="form.subtotal" />
                    <input type="hidden" name="tax_amount" :value="form.tax_amount" />
                    <input type="hidden" name="service_charge" :value="form.service_charge" />
                    <input type="hidden" name="total_amount" :value="form.total_amount" />
                </x-card>

                <x-card title="Payment Method" subtitle="Capture payment details.">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Payment Method</label>
                            <select name="payment_method" x-model="form.payment_method" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                                @foreach(['CASH','CARD','MOBILE','ROOM_CHARGE'] as $method)
                                    <option value="{{ $method }}">{{ str_replace('_',' ', $method) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="form.payment_method === 'ROOM_CHARGE'" x-cloak>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Linked Folio</label>
                            <input type="text" name="folio_reference" placeholder="Folio number" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
                            <p class="text-xs text-slate-400 mt-1">Charge will be posted to the guest folio.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Notes</label>
                            <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200"></textarea>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('tenant.pos.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600">Submit Order</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function posOrderBuilder({ menuItems, rooms, defaultType }) {
            return {
                menuItems,
                rooms,
                form: {
                    order_type: defaultType || 'DINE_IN',
                    guest_count: 1,
                    table_number: '',
                    room_id: null,
                    items: [],
                    subtotal: 0,
                    tax_amount: 0,
                    service_charge: 0,
                    discount_amount: 0,
                    total_amount: 0,
                    payment_method: 'CASH',
                },
                handleTypeChange() {
                    if (this.form.order_type !== 'ROOM_SERVICE') {
                        this.form.room_id = null;
                    }
                    if (this.form.order_type !== 'DINE_IN') {
                        this.form.table_number = '';
                    }
                },
                addItem(item) {
                    const existing = this.form.items.find(i => i.id === item.id);
                    if (existing) {
                        existing.quantity += 1;
                        existing.total = existing.quantity * existing.price;
                    } else {
                        this.form.items.push({
                            uid: crypto.randomUUID(),
                            id: item.id,
                            name: item.name,
                            category: item.category,
                            price: parseFloat(item.price),
                            quantity: 1,
                            total: parseFloat(item.price),
                        });
                    }
                    this.recalculate();
                },
                increment(index) {
                    this.form.items[index].quantity++;
                    this.form.items[index].total = this.form.items[index].quantity * this.form.items[index].price;
                    this.recalculate();
                },
                decrement(index) {
                    if (this.form.items[index].quantity > 1) {
                        this.form.items[index].quantity--;
                        this.form.items[index].total = this.form.items[index].quantity * this.form.items[index].price;
                        this.recalculate();
                    }
                },
                remove(index) {
                    this.form.items.splice(index, 1);
                    this.recalculate();
                },
                recalculate() {
                    const subtotal = this.form.items.reduce((sum, item) => sum + item.quantity * item.price, 0);
                    this.form.subtotal = subtotal;
                    this.form.tax_amount = subtotal * 0.16;
                    this.form.service_charge = subtotal * 0.05;
                    const discount = parseFloat(this.form.discount_amount || 0);
                    this.form.total_amount = subtotal + this.form.tax_amount + this.form.service_charge - discount;
                },
                currency(value) {
                    return `{{ $currency ?? 'USD' }} ${Number(value || 0).toFixed(2)}`;
                }
            }
        }
    </script>
@endpush
