<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexPOS — Iniciar Sesión</title>
    <script>
        // Apply dark mode before CSS loads to prevent flash
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 flex items-center justify-center p-4"
      x-data="{ dark: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('dark', v => { document.documentElement.classList.toggle('dark', v); localStorage.setItem('darkMode', v) })">

    <div class="w-full max-w-sm">

        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 mb-4 shadow-lg">
                <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">NexPOS</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistema de Punto de Venta</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-8">

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-700 dark:text-emerald-400">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="label">Correo electrónico</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           class="input @error('email') border-red-500 focus:ring-red-500 @enderror"
                           placeholder="usuario@ejemplo.com">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="label">Contraseña</label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           class="input @error('password') border-red-500 focus:ring-red-500 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                               name="remember"
                               id="remember_me"
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Recordarme</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary w-full justify-center py-2.5 text-sm font-semibold">
                    Iniciar sesión
                </button>
            </form>
        </div>

        {{-- Demo credentials hint --}}
        <div class="mt-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Credenciales de demo</p>
            <div class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Administrador</span>
                    <span class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">admin@nexpos.com</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Cajero</span>
                    <span class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">cajero@nexpos.com</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Mecánico</span>
                    <span class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">mecanico@nexpos.com</span>
                </div>
                <p class="text-gray-400 dark:text-gray-500 pt-2 border-t border-gray-100 dark:border-gray-800 mt-1">
                    Contraseña para todos: <span class="font-mono font-semibold text-gray-600 dark:text-gray-300">password</span>
                </p>
            </div>
        </div>

        {{-- Dark mode toggle --}}
        <div class="mt-4 flex justify-center">
            <button @click="dark = !dark"
                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <template x-if="!dark">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </template>
                <template x-if="dark">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </template>
                <span x-text="dark ? 'Modo claro' : 'Modo oscuro'"></span>
            </button>
        </div>

    </div>

    {{-- En login no hay Livewire, así que iniciamos Alpine manualmente --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => { window.Alpine && Alpine.start(); });
    </script>
</body>
</html>
