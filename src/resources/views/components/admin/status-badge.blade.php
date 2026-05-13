@props(['tone' => 'neutral'])

@php
    $tones = [
        'violet' => 'bg-[#e6deff] text-[#4a3597]',
        'green' => 'bg-[#eef7f2] text-[#1f6b52]',
        'amber' => 'bg-[#fff3cd] text-[#745800]',
        'red' => 'bg-[#ffdad6] text-[#93000a]',
        'gray' => 'bg-[#ece7ef] text-slate-600',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-3 py-1 text-xs font-medium '.($tones[$tone] ?? $tones['gray'])]) }}>{{ $slot }}</span>
