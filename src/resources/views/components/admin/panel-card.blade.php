@props(['title' => null, 'description' => null, 'padding' => 'p-6'])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-[1.75rem] border border-[#d8d2de] bg-white']) }}>
    @if ($title || $description || isset($actions))
        <div class="flex flex-col gap-4 border-b border-[#ebe6ef] px-6 py-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                @if ($title)
                    <h2 class="font-display text-2xl text-slate-900">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex flex-wrap gap-3">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">{{ $slot }}</div>
</section>
