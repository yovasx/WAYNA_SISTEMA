<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $titulo }} | WAYNA</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#fdf8ff] text-slate-900 antialiased">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="font-display text-3xl text-[#5f4cae]">WAYNA</a>

            <section class="mt-10 rounded-[2rem] border border-[#d8d2de] bg-white p-8">
                <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">Seccion en preparacion</p>
                <h1 class="mt-3 font-display text-4xl text-slate-900">{{ $titulo }}</h1>
                <p class="mt-4 max-w-2xl text-base text-slate-600">{{ $descripcion }}</p>
                <a href="{{ route('home') }}" class="mt-8 inline-flex rounded-2xl bg-[#5f4cae] px-5 py-3 text-sm font-medium text-white">Volver al inicio</a>
            </section>
        </div>
    </body>
</html>
