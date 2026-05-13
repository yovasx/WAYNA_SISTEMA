<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WAYNA') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#fdf8ff] text-slate-900 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            <section class="relative hidden overflow-hidden border-r border-[#ddd8e1] bg-[#f1ecf5] lg:flex lg:flex-col lg:justify-between lg:p-12">
                <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(95,76,174,0.25) 1px, transparent 1px), linear-gradient(90deg, rgba(95,76,174,0.25) 1px, transparent 1px); background-size: 32px 32px;"></div>
                <a href="{{ route('home') }}" wire:navigate class="relative font-display text-4xl text-[#5f4cae]">WAYNA</a>

                <div class="relative mx-auto max-w-lg">
                    <p class="font-mono-data text-xs uppercase tracking-[0.35em] text-slate-500">Comercio con identidad</p>
                    <h1 class="mt-5 font-display text-5xl leading-tight text-slate-900">Herencia boliviana en una experiencia digital serena.</h1>
                    <p class="mt-5 text-lg leading-8 text-slate-600">Accede como administrador, emprendedor o usuario y entra a un entorno pensado para vender, descubrir y acompanar historias artesanales reales.</p>
                </div>

                <div class="relative grid grid-cols-2 gap-4 text-sm text-slate-600">
                    <div class="rounded-[1.5rem] border border-[#d9d1e5] bg-white p-5">
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Admin</p>
                        <p class="mt-3 font-display text-2xl text-slate-900">Gestion</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-[#d9d1e5] bg-white p-5">
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Emprendedor</p>
                        <p class="mt-3 font-display text-2xl text-slate-900">Catalogo</p>
                    </div>
                </div>
            </section>

            <section class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-12">
                <div class="w-full max-w-xl rounded-[2rem] border border-[#ddd8e1] bg-white p-6 shadow-sm sm:p-10">
                    {{ $slot }}
                </div>
            </section>
        </div>
    </body>
</html>
