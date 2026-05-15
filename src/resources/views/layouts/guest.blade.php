<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WAYNA') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="wayna-shell bg-surface text-ink antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            <section class="relative hidden overflow-hidden border-r border-stroke bg-surface-soft lg:flex lg:flex-col lg:justify-between lg:p-12">
                <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(rgba(91,75,196,0.16) 1px, transparent 1px), linear-gradient(90deg, rgba(184,106,79,0.14) 1px, transparent 1px); background-size: 32px 32px;"></div>
                <a href="{{ route('home') }}" wire:navigate class="relative font-display text-4xl text-primary-600">WAYNA</a>

                <div class="relative mx-auto max-w-lg">
                    <p class="font-mono-data text-xs uppercase tracking-[0.35em] text-ink-muted">Comercio con identidad</p>
                    <h1 class="mt-5 font-display text-5xl leading-tight text-ink">Herencia boliviana en una experiencia digital serena.</h1>
                    <p class="mt-5 text-lg leading-8 text-ink-soft">Accede como administrador, emprendedor o usuario y entra a un entorno pensado para vender, descubrir y acompanar historias artesanales reales.</p>
                </div>

                <div class="relative grid grid-cols-2 gap-4 text-sm text-ink-soft">
                    <div class="wayna-card p-5">
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-ink-muted">Admin</p>
                        <p class="mt-3 font-display text-2xl text-ink">Gestion</p>
                    </div>
                    <div class="wayna-card p-5">
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-ink-muted">Emprendedor</p>
                        <p class="mt-3 font-display text-2xl text-ink">Catalogo</p>
                    </div>
                </div>
            </section>

            <section class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-12">
                <div class="wayna-card w-full max-w-xl rounded-[2rem] p-6 sm:p-10">
                    {{ $slot }}
                </div>
            </section>
        </div>
    </body>
</html>
