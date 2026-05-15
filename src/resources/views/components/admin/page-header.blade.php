@props(['eyebrow' => null, 'title', 'description' => null])

<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
        @if ($eyebrow)
            <p class="font-mono-data text-xs uppercase tracking-[0.28em] text-ink-muted">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-2 font-display text-4xl text-ink lg:text-5xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-3 max-w-3xl text-sm leading-7 text-ink-soft">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap gap-3">{{ $actions }}</div>
    @endisset
</div>
