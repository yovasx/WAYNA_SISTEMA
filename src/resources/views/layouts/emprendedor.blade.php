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
    <body class="bg-[#fdf8ff] text-slate-900 antialiased">
        <header class="fixed inset-x-0 top-0 z-40 border-b border-[#d9d1e5] bg-[#fdf8ff]/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('dashboard') }}" class="font-display text-2xl font-semibold text-[#5f4cae]">WAYNA</a>

                <nav class="hidden items-center gap-6 md:flex">
                    <span class="text-sm text-slate-500 transition hover:text-[#5f4cae]">Catalog</span>
                    <span class="text-sm text-slate-500 transition hover:text-[#5f4cae]">Entrepreneurs</span>
                    <span class="text-sm text-slate-500 transition hover:text-[#5f4cae]">Donate</span>
                </nav>

                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <span class="material-symbols-outlined text-[#5f4cae]">shopping_cart</span>
                    <span class="material-symbols-outlined text-[#5f4cae]">notifications</span>
                    <span class="hidden rounded-full border border-[#d9d1e5] bg-white px-3 py-1 md:inline-flex">
                        {{ auth()->user()->name }}
                    </span>

                    <a href="{{ route('profile') }}" class="rounded-full border border-[#d9d1e5] px-3 py-1 hover:border-[#5f4cae] hover:text-[#5f4cae]">Perfil</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-[#d9d1e5] px-3 py-1 hover:border-[#5f4cae] hover:text-[#5f4cae]">Salir</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="pt-16 md:flex">
            <aside class="hidden w-[260px] shrink-0 border-r border-[#d9d1e5] bg-white md:block">
                <div class="flex h-[calc(100vh-4rem)] flex-col px-5 py-6">
                    <div class="px-2 pb-6">
                        <h2 class="font-display text-2xl text-[#5f4cae]">WAYNA</h2>
                        <p class="text-sm text-slate-500">Bolivian Artisanal Heritage</p>
                    </div>

                    <nav class="space-y-2 text-sm text-slate-700">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-[#f7f2fb] hover:text-[#5f4cae]"><span class="material-symbols-outlined">home</span>Home</a>
                        <a href="{{ route('dashboard.emprendedor') }}" class="flex items-center gap-3 rounded-2xl bg-[#7865c9] px-4 py-3 font-medium text-white"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-[#f7f2fb] hover:text-[#5f4cae]"><span class="material-symbols-outlined">settings</span>Perfil</a>
                        <span class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-400"><span class="material-symbols-outlined">auto_awesome</span>Impact</span>
                    </nav>

                    <div class="mt-auto rounded-3xl border border-[#efc2bb] bg-[#ffdad6] p-4 text-sm text-[#812914]">
                        Tu panel esta listo para categorias y productos, incluso si hoy empiezas desde cero.
                    </div>
                </div>
            </aside>

            <main class="min-h-[calc(100vh-4rem)] flex-1">{{ $slot }}</main>
        </div>

        @livewireScripts
    </body>
</html>
