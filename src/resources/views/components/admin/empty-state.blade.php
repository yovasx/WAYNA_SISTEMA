@props(['title', 'description', 'icon' => 'inventory'])

<div {{ $attributes->merge(['class' => 'rounded-[1.5rem] border border-dashed border-[#d8d2de] bg-[#fcfbfe] px-6 py-10 text-center']) }}>
    <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#f1ecf5] text-[#5f4cae]">
        <span class="material-symbols-outlined">{{ $icon }}</span>
    </div>
    <p class="mt-4 font-display text-2xl text-slate-900">{{ $title }}</p>
    <p class="mx-auto mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $description }}</p>

    @isset($action)
        <div class="mt-5 flex justify-center">{{ $action }}</div>
    @endisset
</div>
