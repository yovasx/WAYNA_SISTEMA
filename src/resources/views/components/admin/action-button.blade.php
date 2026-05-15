@props(['icon', 'label', 'tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'border-stroke text-ink-soft hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700',
        'primary' => 'border-primary-200 bg-primary-50 text-primary-700 hover:border-primary-400 hover:bg-primary-100',
        'success' => 'border-success-100 bg-success-50 text-success-700 hover:border-success-500 hover:bg-success-100',
        'danger' => 'border-danger-100 bg-danger-50 text-danger-700 hover:border-danger-500 hover:bg-danger-100',
        'warning' => 'border-accent-100 bg-accent-50 text-accent-700 hover:border-accent-400 hover:bg-accent-100',
    ];

    $classes = $tones[$tone] ?? $tones['neutral'];
@endphp

<button
    type="button"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    {{ $attributes->merge(['class' => 'wayna-icon-btn '.$classes]) }}
>
    <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
    <span class="sr-only">{{ $label }}</span>
</button>
