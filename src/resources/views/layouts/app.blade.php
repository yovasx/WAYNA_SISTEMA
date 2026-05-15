<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WAYNA') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="wayna-shell antialiased">
        <div class="min-h-screen bg-transparent">
            <livewire:layout.navigation />

            @if (isset($header))
                <header class="border-b border-stroke bg-surface/90 backdrop-blur">
                    <div class="px-4 py-6 sm:px-6 xl:px-10 2xl:px-14">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
