<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="nexposApp()"
      x-init="init()"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\BusinessSetting::get('app_name', 'NexPOS') }} — @yield('title', 'Dashboard')</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans antialiased">

{{-- Toast Notifications --}}
<div
    x-data="{ toasts: [] }"
    x-on:toast.window="
        toasts.push($event.detail);
        setTimeout(() => toasts.splice(toasts.indexOf($event.detail), 1), 3500)
    "
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80 pointer-events-none">
    <template x-for="(toast, i) in toasts" :key="i">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-10"
             x-transition:enter-end="opacity-100 translate-x-0"
             :class="{
                'bg-emerald-600': toast.type === 'success',
                'bg-red-600':     toast.type === 'error',
                'bg-amber-500':   toast.type === 'warning',
                'bg-blue-600':    toast.type === 'info',
             }"
             class="flex items-center gap-3 text-white px-4 py-3 rounded-xl shadow-2xl text-sm pointer-events-auto">
            <span x-text="{ success:'✓', error:'✕', warning:'⚠', info:'ℹ' }[toast.type] || 'ℹ'" class="text-base font-bold shrink-0"></span>
            <span x-text="toast.message" class="flex-1"></span>
        </div>
    </template>
</div>

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
           class="flex-shrink-0 flex flex-col bg-gray-900 text-gray-100 transition-all duration-300 overflow-hidden z-30">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-[18px] border-b border-white/10 h-14">
            <div class="w-8 h-8 flex-shrink-0 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.268a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .895-.143z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span x-show="sidebarOpen" x-cloak class="font-bold text-base tracking-tight text-white whitespace-nowrap">{{ \App\Models\BusinessSetting::get('app_name', 'NexPOS') }}</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

            @php
            $nav = [
                ['route' => 'dashboard',       'label' => 'Dashboard',        'permission' => 'view dashboard',  'icon' => 'dashboard', 'enabled' => true],
                ['route' => 'pos.index',        'label' => 'Punto de Venta',   'permission' => 'create sales',    'icon' => 'pos',  'enabled' => true],
                ['route' => 'sales.index',      'label' => 'Ventas',           'permission' => 'view sales',      'icon' => 'sales',    'enabled' => true],
                ['route' => 'products.index',   'label' => 'Inventario',       'permission' => 'view products',   'icon' => 'inventory',    'enabled' => true],
                ['route' => 'categories.index', 'label' => 'Categorías',       'permission' => 'view categories', 'icon' => 'categories',   'enabled' => true],
                ['route' => 'reports.index',    'label' => 'Reportes',         'permission' => 'view reports',    'icon' => 'reports',  'enabled' => true],
                ['route' => 'customers.index',  'label' => 'Clientes',         'permission' => 'view customers',  'icon' => 'customers',    'enabled' => false],
                ['route' => 'workshop.index',   'label' => 'Taller',           'permission' => 'view workshop',   'icon' => 'workshop', 'enabled' => false],
                ['route' => 'employees.index',  'label' => 'Empleados',        'permission' => 'view employees',  'icon' => 'employees',    'enabled' => false],
                ['route' => 'users.index',      'label' => 'Usuarios',         'permission' => 'view users',      'icon' => 'users',    'enabled' => false],
                ['route' => 'settings.index',   'label' => 'Configuración',    'permission' => 'view settings',   'icon' => 'settings', 'enabled' => true],
            ];
            @endphp

            @foreach($nav as $item)

                @can($item['permission'])

                @php
                    $isActive = request()->routeIs(str_replace('index', '*', $item['route']));
                    $isEnabled = $item['enabled'];
                @endphp

                <a href="{{ $isEnabled ? route($item['route']) : '#' }}"
                @if(!$isEnabled) onclick="return false;" @endif
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group

                {{ !$isEnabled
                        ? 'opacity-40 cursor-not-allowed text-gray-500 bg-white/5'
                        : ($isActive
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-gray-400 hover:text-white hover:bg-white/10')
                }}">

                    @include('components.nav-icon', [
                        'icon' => $item['icon'],
                        'active' => $isActive
                    ])

                    <span x-show="sidebarOpen" x-cloak class="whitespace-nowrap flex items-center gap-2">
                        {{ $item['label'] }}

                        @if(!$isEnabled)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-700 text-gray-300">
                                Próximamente
                            </span>
                        @endif
                    </span>
                </a>

                @endcan

            @endforeach

        </nav>

        {{-- User footer --}}
        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 flex-shrink-0 bg-indigo-500 rounded-full flex items-center justify-center text-xs font-bold text-white uppercase">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div x-show="sidebarOpen" x-cloak class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize truncate">{{ auth()->user()->getRoleNames()->first() ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" x-cloak>
                    @csrf
                    <button type="submit" title="Cerrar sesión"
                            class="p-1 text-gray-500 hover:text-red-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN AREA ── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top Navbar --}}
        <header class="flex-shrink-0 flex items-center justify-between h-14 px-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-sm font-semibold text-gray-700 dark:text-gray-200">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2">
                @php $lowStock = \App\Models\Product::where('track_stock', true)->whereRaw('stock <= min_stock')->where('is_active', true)->count(); @endphp
                @if($lowStock > 0)
                <a href="{{ route('products.index', ['filter' => 'low_stock']) }}"
                   class="flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ $lowStock }} bajo stock
                </a>
                @endif

                <button @click="darkMode = !darkMode"
                        class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950 p-4 md:p-6">
            @if(session('success'))
                <div x-data x-init="$dispatch('toast', { type: 'success', message: @js(session('success')) })"></div>
            @endif
            @if(session('error'))
                <div x-data x-init="$dispatch('toast', { type: 'error', message: @js(session('error')) })"></div>
            @endif
            @if(session('warning'))
                <div x-data x-init="$dispatch('toast', { type: 'warning', message: @js(session('warning')) })"></div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
function nexposApp() {
    return {
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
        darkMode: localStorage.getItem('darkMode') === 'true',
        init() {
            this.$watch('sidebarOpen', v => localStorage.setItem('sidebarOpen', v));
            this.$watch('darkMode', v => localStorage.setItem('darkMode', v));
        }
    }
}
</script>

@stack('scripts')
</body>
</html>
