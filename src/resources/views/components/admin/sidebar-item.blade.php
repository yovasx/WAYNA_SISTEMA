@props(['active' => false, 'icon'])

@php
    $anchorClasses = $active
        ? 'bg-primary-500 text-white shadow-wayna'
        : 'text-ink-soft hover:bg-primary-50 hover:text-primary-700';

    $iconClasses = $active ? 'text-white' : 'text-ink-muted group-hover:text-primary-700';
@endphp

<a {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition '.$anchorClasses]) }} :class="sidebarEffective === 'expanded' ? 'justify-start' : 'justify-center px-2'" @click="mobileSidebarOpen = false">
    <span class="material-symbols-outlined {{ $iconClasses }} text-[22px]">{{ $icon }}</span>
    <span x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms class="truncate">
        {{ $slot }}
    </span>
</a>
