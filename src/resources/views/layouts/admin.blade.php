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
    </head>
    <body x-data="adminLayoutState()" x-init="init()" class="bg-[#fdf8ff] text-slate-900 antialiased">
        <div class="min-h-screen bg-[#fdf8ff]">
            <x-admin.topbar />

            <div x-cloak x-show="mobileSidebarOpen" class="fixed inset-0 z-30 bg-slate-950/30 backdrop-blur-sm md:hidden" @click="closeMobileSidebar()"></div>

            <div class="flex pt-16">
                <x-admin.sidebar />

                <main class="min-w-0 flex-1 px-4 py-8 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
