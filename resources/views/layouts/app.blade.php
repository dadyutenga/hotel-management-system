<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' | ' : '' }}Hotel Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-Iu0fKx5fplLWYwgd0CQpZK0s8AjtKoa6HgMHqYjgJv1nVbWcv16O3Qe9n1VWeItPxO2VINqbodIZ6Tn6k9a7Vg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>[x-cloak]{display:none!important;}</style>
</head>
<body class="font-[Inter] bg-slate-100 text-slate-800">
    @php
        $user = auth()->user();
        $role = $user?->role?->name;
        $tenant = $user?->tenant;
        $property = $user?->property;
        $navigation = [
            [
                'label' => 'Dashboard',
                'icon' => 'fa-solid fa-gauge-high',
                'route' => 'tenant.reports.index',
                'roles' => null,
            ],
            [
                'label' => 'Guests',
                'icon' => 'fa-solid fa-user-group',
                'route' => 'tenant.guests.index',
                'roles' => ['DIRECTOR', 'MANAGER', 'SUPERVISOR', 'RECEPTIONIST'],
            ],
            [
                'label' => 'Reservations',
                'icon' => 'fa-solid fa-calendar-check',
                'route' => 'tenant.reservations.index',
                'roles' => ['DIRECTOR', 'MANAGER', 'SUPERVISOR', 'RECEPTIONIST'],
            ],
            [
                'label' => 'Folios',
                'icon' => 'fa-solid fa-clipboard-list',
                'route' => 'tenant.folios.show',
                'route_params' => fn () => request()->route('folio'),
                'roles' => ['DIRECTOR', 'MANAGER', 'SUPERVISOR', 'ACCOUNTANT'],
                'disabled' => true,
            ],
            [
                'label' => 'Invoices',
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'route' => 'tenant.invoices.index',
                'roles' => ['DIRECTOR', 'MANAGER', 'ACCOUNTANT'],
            ],
            [
                'label' => 'Housekeeping',
                'icon' => 'fa-solid fa-broom',
                'route' => 'tenant.housekeeping.index',
                'roles' => ['DIRECTOR', 'MANAGER', 'SUPERVISOR', 'HOUSEKEEPER'],
            ],
            [
                'label' => 'Maintenance',
                'icon' => 'fa-solid fa-screwdriver-wrench',
                'route' => 'tenant.maintenance.index',
                'roles' => ['DIRECTOR', 'MANAGER', 'SUPERVISOR'],
            ],
            [
                'label' => 'POS',
                'icon' => 'fa-solid fa-cash-register',
                'route' => 'tenant.pos.index',
                'roles' => ['DIRECTOR', 'MANAGER', 'SUPERVISOR', 'BAR_TENDER', 'RECEPTIONIST'],
            ],
            [
                'label' => 'Reports',
                'icon' => 'fa-solid fa-chart-line',
                'route' => 'tenant.reports.revenue',
                'roles' => ['DIRECTOR', 'MANAGER', 'SUPERVISOR', 'ACCOUNTANT'],
            ],
        ];

        $allowedNavigation = collect($navigation)->filter(function ($item) use ($role) {
            if (($item['roles'] ?? null) === null) {
                return true;
            }
            return in_array($role, $item['roles']);
        });
    @endphp

    <div class="min-h-screen flex">
        <aside class="hidden lg:block w-72 bg-slate-900 text-white shadow-xl">
            <div class="px-6 py-6 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-amber-400 text-slate-900 flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($tenant?->name ?? 'HM', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-widest text-slate-400">{{ $tenant?->code ?? 'Tenant' }}</p>
                        <p class="text-lg font-semibold">{{ $tenant?->name ?? 'Hotel Management' }}</p>
                    </div>
                </div>
                @if($property)
                    <p class="mt-4 text-sm text-slate-400">
                        <i class="fa-solid fa-hotel mr-2"></i>{{ $property->name }}
                    </p>
                @endif
            </div>
            <nav class="px-3 py-6 space-y-1 overflow-y-auto h-[calc(100vh-120px)]">
                @foreach($allowedNavigation as $item)
                    @php
                        $isDisabled = $item['disabled'] ?? false;
                        $routeName = $item['route'];
                        $params = [];
                        if(isset($item['route_params']) && is_callable($item['route_params'])) {
                            $params = array_filter([$item['route_params']()]);
                        }
                        $url = $isDisabled ? '#' : (Route::has($routeName) ? route($routeName, $params) : '#');
                        $isActive = !$isDisabled && request()->routeIs($routeName . '*');
                    @endphp
                    <a
                        href="{{ $url }}"
                        @class([
                            'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-150',
                            'bg-amber-400 text-slate-900 font-semibold shadow-md' => $isActive,
                            'text-slate-300 hover:bg-slate-800 hover:text-white' => ! $isActive,
                            'cursor-not-allowed opacity-50 pointer-events-none' => $isDisabled,
                        ])
                    >
                        <i class="{{ $item['icon'] }} w-5 text-center"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button class="lg:hidden text-slate-500" x-data="{ open: false }" @click="open = true">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        <div>
                            <p class="text-sm text-slate-500">{{ now()->format('l, d M Y') }}</p>
                            <h1 class="text-xl font-semibold">{{ $pageTitle ?? 'Overview' }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-sm text-right">
                            <p class="font-semibold">{{ $user?->full_name ?? $user?->username }}</p>
                            <p class="text-slate-500 uppercase tracking-wide text-xs">{{ $role }}</p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 border-t border-b border-slate-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 text-slate-500">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $property?->address_line1 ?? 'No property selected' }}</span>
                        </div>
                        <div class="text-slate-400">
                            Tenant ID: {{ $tenant?->id ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @if (session('status'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-3">
                        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                            <p class="font-semibold mb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i>There were some problems with your submission</p>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </header>
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @isset($breadcrumbs)
                        <nav class="flex text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center gap-2">
                                <li><a href="{{ route('dashboard') }}" class="hover:text-slate-700">Home</a></li>
                                @foreach ($breadcrumbs as $breadcrumb)
                                    <li><i class="fa-solid fa-chevron-right text-xs text-slate-400"></i></li>
                                    <li>
                                        @if(isset($breadcrumb['url']))
                                            <a href="{{ $breadcrumb['url'] }}" class="hover:text-slate-700">{{ $breadcrumb['label'] }}</a>
                                        @else
                                            <span class="text-slate-700">{{ $breadcrumb['label'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endisset

                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
