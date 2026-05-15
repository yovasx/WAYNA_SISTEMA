@props(['label', 'value', 'icon' => 'insights', 'tone' => 'primary', 'helper' => null])

@php
    $tones = [
        'primary' => ['chip' => 'bg-primary-50 text-primary-700', 'value' => 'text-primary-600'],
        'secondary' => ['chip' => 'bg-accent-50 text-accent-700', 'value' => 'text-accent-600'],
        'tertiary' => ['chip' => 'bg-surface-soft text-accent-700', 'value' => 'text-accent-700'],
        'neutral' => ['chip' => 'bg-surface-soft text-ink-soft', 'value' => 'text-ink'],
        'green' => ['chip' => 'bg-success-100 text-success-700', 'value' => 'text-success-600'],
        'red' => ['chip' => 'bg-danger-100 text-danger-700', 'value' => 'text-danger-600'],
        'amber' => ['chip' => 'bg-accent-50 text-accent-700', 'value' => 'text-accent-700'],
    ];

    $palette = $tones[$tone] ?? $tones['primary'];
@endphp

<article class="wayna-card p-5">
    <div class="flex items-center justify-between gap-3">
        <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $palette['chip'] }}">
            <span class="material-symbols-outlined">{{ $icon }}</span>
        </div>
        @if ($helper)
            <span class="text-xs text-ink-muted">{{ $helper }}</span>
        @endif
    </div>

    <p class="mt-5 font-mono-data text-xs uppercase tracking-[0.24em] text-ink-muted">{{ $label }}</p>
    <p class="mt-3 font-display text-3xl {{ $palette['value'] }}">{{ $value }}</p>
</article>
