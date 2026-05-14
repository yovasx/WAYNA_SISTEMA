@props(['icon', 'label', 'tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'border-[#d8d2de] text-slate-600 hover:border-[#5f4cae] hover:bg-[#f7f2fb] hover:text-[#5f4cae]',
        'primary' => 'border-[#d8d2de] text-slate-600 hover:border-[#5f4cae] hover:bg-[#f7f2fb] hover:text-[#5f4cae]',
        'success' => 'border-[#cfe6db] text-[#1f6b52] hover:bg-[#eef7f2] hover:border-[#1f6b52]',
        'danger' => 'border-[#f0c7c2] text-[#93000a] hover:bg-[#ffdad6] hover:border-[#93000a]',
        'warning' => 'border-[#f0e0ae] text-[#745800] hover:bg-[#fff3cd] hover:border-[#745800]',
    ];

    $classes = $tones[$tone] ?? $tones['neutral'];
@endphp

<button
    type="button"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    {{ $attributes->merge(['class' => 'inline-flex h-11 w-11 items-center justify-center rounded-2xl border bg-white transition '.$classes]) }}
>
    <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
    <span class="sr-only">{{ $label }}</span>
</button>
