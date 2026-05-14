<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">WAYNA</p>
            <h2 class="mt-2 font-display text-3xl text-slate-900">{{ $titulo }}</h2>
        </div>
    </x-slot>

    <div class="px-4 py-10 sm:px-6 xl:px-10 2xl:px-14">
        <div class="rounded-[2rem] border border-[#d9d1e5] bg-white p-8">
            <p class="max-w-4xl text-base text-slate-600">{{ $subtitulo }}</p>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach ($kpis as $kpi)
                    <div class="rounded-3xl border border-[#e7e0f1] bg-[#fcfbfe] p-5">
                        <p class="font-mono-data text-xs uppercase tracking-[0.3em] text-slate-500">{{ $kpi['label'] }}</p>
                        <p class="mt-4 font-display text-4xl {{ $kpi['tone'] === 'amber' ? 'text-[#745800]' : ($kpi['tone'] === 'green' ? 'text-[#1f6b52]' : 'text-[#5f4cae]') }}">{{ $kpi['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
