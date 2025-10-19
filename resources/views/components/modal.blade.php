@props([
    'id',
    'title' => null,
    'size' => 'max-w-2xl',
])

<div x-data="{ open: false }" x-on:open-modal.window="if($event.detail === '{{ $id }}'){ open = true }" x-on:close-modal.window="if($event.detail === '{{ $id }}'){ open = false }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-slate-900/70" @click="open = false"></div>
    <div class="relative w-full {{ $size }} mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">{{ $title }}</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="open = false">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-4 max-h-[75vh] overflow-y-auto">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
