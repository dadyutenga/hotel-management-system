@props([
    'placeholder' => 'Search...',
    'endpoint' => null,
    'name' => 'search',
    'value' => null,
])

<div x-data="searchComponent({{ json_encode($endpoint) }}, '{{ $name }}', '{{ $value }}')" class="relative">
    <div class="flex items-center bg-white border border-slate-200 rounded-full shadow-sm focus-within:border-amber-400">
        <span class="pl-4 text-slate-400">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input
            type="text"
            x-model="query"
            @input.debounce.300ms="performSearch"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            class="w-full bg-transparent px-4 py-2 text-sm focus:outline-none"
            autocomplete="off"
        />
        <button type="button" x-show="query" @click="clear" class="pr-4 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div x-show="results.length && open" @click.away="open = false" class="absolute z-40 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
        <ul class="divide-y divide-slate-100">
            <template x-for="result in results" :key="result.id">
                <li>
                    <button type="button" class="w-full text-left px-4 py-3 hover:bg-amber-50" @click="select(result)">
                        <p class="text-sm font-semibold text-slate-700" x-text="result.label"></p>
                        <p class="text-xs text-slate-500" x-text="result.description"></p>
                    </button>
                </li>
            </template>
        </ul>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function searchComponent(endpoint, name, initialValue) {
                return {
                    endpoint,
                    name,
                    query: initialValue || '',
                    results: [],
                    open: false,
                    performSearch() {
                        if (!this.endpoint || this.query.length < 2) {
                            this.results = [];
                            this.open = false;
                            return;
                        }
                        fetch(`${this.endpoint}?${new URLSearchParams({ query: this.query })}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        })
                            .then(response => response.json())
                            .then(data => {
                                this.results = data.results || [];
                                this.open = this.results.length > 0;
                            });
                    },
                    select(result) {
                        this.query = result.label;
                        this.open = false;
                        this.$dispatch('search-selected', { name: this.name, value: result });
                    },
                    clear() {
                        this.query = '';
                        this.results = [];
                        this.open = false;
                        this.$dispatch('search-cleared', { name: this.name });
                    }
                }
            }
        </script>
    @endpush
@endonce
