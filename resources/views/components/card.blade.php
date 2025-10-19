@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
])

<div {{ $attributes->class(['bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden']) }}>
    @if($title || $actions)
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-200 bg-slate-50">
            <div>
                @if($title)
                    <h2 class="text-lg font-semibold text-slate-800">{{ $title }}</h2>
                @endif
                @if($subtitle)
                    <p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    <div class="px-6 py-4">
        {{ $slot }}
    </div>
</div>
