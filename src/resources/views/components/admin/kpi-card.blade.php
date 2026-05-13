@props(['label', 'value', 'icon' => 'insights', 'tone' => 'primary', 'helper' => null])

@php
    $tones = [
        'primary' => ['chip' => 'bg-[#e6deff] text-[#4a3597]', 'value' => 'text-[#5f4cae]'],
        'secondary' => ['chip' => 'bg-[#ffdad2] text-[#812914]', 'value' => 'text-[#a03f29]'],
        'tertiary' => ['chip' => 'bg-[#ffdf97] text-[#5a4400]', 'value' => 'text-[#745800]'],
        'neutral' => ['chip' => 'bg-[#f1ecf5] text-slate-600', 'value' => 'text-slate-900'],
    ];

    $palette = $tones[$tone] ?? $tones['primary'];
@endphp

<article class="rounded-[1.75rem] border border-[#d8d2de] bg-white p-5">
    <div class="flex items-center justify-between gap-3">
        <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $palette['chip'] }}">
            <span class="material-symbols-outlined">{{ $icon }}</span>
        </div>
        @if ($helper)
            <span class="text-xs text-slate-500">{{ $helper }}</span>
        @endif
    </div>

    <p class="mt-5 font-mono-data text-xs uppercase tracking-[0.24em] text-slate-500">{{ $label }}</p>
    <p class="mt-3 font-display text-3xl {{ $palette['value'] }}">{{ $value }}</p>
</article>
