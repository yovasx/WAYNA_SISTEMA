@props(['tone' => 'neutral'])

@php
    $tones = [
        'violet' => 'bg-primary-50 text-primary-700',
        'green' => 'bg-success-100 text-success-700',
        'amber' => 'bg-accent-50 text-accent-700',
        'red' => 'bg-danger-100 text-danger-700',
        'gray' => 'bg-surface-soft text-ink-soft',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-3 py-1 text-xs font-medium '.($tones[$tone] ?? $tones['gray'])]) }}>{{ $slot }}</span>
