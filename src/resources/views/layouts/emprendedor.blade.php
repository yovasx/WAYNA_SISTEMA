<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($pageTitle ?? 'Panel emprendedor').' | '.config('app.name', 'WAYNA') }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body x-data="adminLayoutState()" x-init="init()" class="wayna-shell bg-surface text-ink antialiased">
        <div class="grid min-h-screen grid-rows-[4rem_minmax(0,1fr)] bg-transparent">
            <x-emprendedor.topbar :page-title="$pageTitle ?? 'Panel emprendedor'" />

            <div x-cloak x-show="mobileSidebarOpen" class="fixed inset-0 z-30 bg-stone-950/35 backdrop-blur-sm md:hidden" @click="closeMobileSidebar()"></div>

            <div class="min-h-0 md:grid md:grid-cols-[88px_minmax(0,1fr)] md:transition-[grid-template-columns] md:duration-300 md:ease-out" :style="desktopGridStyle">
                <x-emprendedor.sidebar />

                <main class="min-h-0 min-w-0 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
