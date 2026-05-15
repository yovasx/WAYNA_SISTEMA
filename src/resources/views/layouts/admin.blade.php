<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($pageTitle ?? 'Admin').' | '.config('app.name', 'WAYNA') }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @if (session('admin_status') || session('admin_error'))
            <div data-toast class="fixed right-6 top-20 z-[100] flex max-w-md items-center gap-3 rounded-2xl border px-5 py-4 shadow-lg transition-all duration-500 ease-out"
                 :class="session('admin_status') ? 'border-success-100 bg-success-100' : 'border-danger-100 bg-danger-100'">
                <span class="material-symbols-outlined {{ session('admin_status') ? 'text-success-700' : 'text-danger-700' }}">
                    {{ session('admin_status') ? 'check_circle' : 'error' }}
                </span>
                <p class="text-sm font-medium {{ session('admin_status') ? 'text-success-700' : 'text-danger-700' }}">
                    {{ session('admin_status') ?? session('admin_error') }}
                </p>
            </div>
        @endif
    </head>
    <body x-data="adminLayoutState()" x-init="init()" class="wayna-shell bg-surface text-ink antialiased">
        <div class="grid min-h-screen grid-rows-[4rem_minmax(0,1fr)] bg-transparent">
            <x-admin.topbar />

            <div x-cloak x-show="mobileSidebarOpen" class="fixed inset-0 z-30 bg-stone-950/35 backdrop-blur-sm md:hidden" @click="closeMobileSidebar()"></div>

            <div class="min-h-0 md:grid md:transition-[grid-template-columns] md:duration-300 md:ease-out" :style="desktopGridStyle">
                <x-admin.sidebar />

                <main class="min-h-0 min-w-0 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
