<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WAYNA') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="wayna-shell bg-surface text-ink antialiased">
        <header class="fixed inset-x-0 top-0 z-40 border-b border-stroke bg-surface/95 backdrop-blur">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6 xl:px-10 2xl:px-14">
                <a href="{{ route('dashboard') }}" class="font-display text-2xl font-semibold text-primary-600">WAYNA</a>

                <nav class="hidden items-center gap-6 md:flex">
                    <span class="text-sm text-ink-muted transition hover:text-primary-700">Catalogo</span>
                    <span class="text-sm text-ink-muted transition hover:text-primary-700">Emprendedores</span>
                    <span class="text-sm text-ink-muted transition hover:text-primary-700">Donar</span>
                </nav>

                <div class="flex items-center gap-3 text-sm text-ink-soft">
                    <span class="material-symbols-outlined text-primary-600">shopping_cart</span>
                    <span class="material-symbols-outlined text-primary-600">notifications</span>
                    <span class="hidden rounded-full border border-stroke bg-surface-raised px-3 py-1 md:inline-flex">
                        {{ auth()->user()->name }}
                    </span>

                    <a href="{{ route('profile') }}" class="rounded-full border border-stroke bg-surface-raised px-3 py-1 transition hover:border-primary-300 hover:text-primary-700">Perfil</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-stroke bg-surface-raised px-3 py-1 transition hover:border-danger-300 hover:text-danger-700">Salir</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="pt-16 md:flex">
            <aside class="hidden w-[260px] shrink-0 border-r border-stroke bg-surface-raised md:block">
                <div class="flex h-[calc(100vh-4rem)] flex-col px-5 py-6">
                    <div class="px-2 pb-6">
                        <h2 class="font-display text-2xl text-primary-600">WAYNA</h2>
                        <p class="text-sm text-ink-muted">Legado artesanal boliviano</p>
                    </div>

                    <nav class="space-y-2 text-sm text-ink-soft">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-primary-50 hover:text-primary-700"><span class="material-symbols-outlined">home</span>Inicio</a>
                        <a href="{{ route('dashboard.emprendedor') }}" class="flex items-center gap-3 rounded-2xl bg-primary-500 px-4 py-3 font-medium text-white shadow-wayna"><span class="material-symbols-outlined">dashboard</span>Panel</a>
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-primary-50 hover:text-primary-700"><span class="material-symbols-outlined">settings</span>Perfil</a>
                        <span class="flex items-center gap-3 rounded-2xl px-4 py-3 text-ink-muted"><span class="material-symbols-outlined">auto_awesome</span>Impacto</span>
                    </nav>

                    <div class="mt-auto rounded-3xl border border-accent-100 bg-accent-50 p-4 text-sm text-accent-700 shadow-wayna-soft">
                        Tu panel esta listo para categorias y productos, incluso si hoy empiezas desde cero.
                    </div>
                </div>
            </aside>

            <main class="min-h-[calc(100vh-4rem)] min-w-0 flex-1">{{ $slot }}</main>
        </div>

        @livewireScripts
    </body>
</html>
