@props(['title' => null, 'description' => null, 'padding' => 'p-6'])

<section {{ $attributes->merge(['class' => 'wayna-card overflow-hidden']) }}>
    @if ($title || $description || isset($actions))
        <div class="flex flex-col gap-4 border-b border-stroke-soft bg-surface-soft/80 px-6 py-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                @if ($title)
                    <h2 class="font-display text-2xl text-ink">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-2 text-sm leading-6 text-ink-soft">{{ $description }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex flex-wrap gap-3">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">{{ $slot }}</div>
</section>
