@props(['active' => false, 'icon'])

@php
    $anchorClasses = $active
        ? 'bg-[#7865c9] text-white shadow-sm'
        : 'text-slate-600 hover:bg-[#f7f2fb] hover:text-[#5f4cae]';

    $iconClasses = $active ? 'text-white' : 'text-slate-400 group-hover:text-[#5f4cae]';
@endphp

<a {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition '.$anchorClasses]) }} :class="sidebarEffective === 'expanded' ? 'justify-start' : 'justify-center px-2'" @click="mobileSidebarOpen = false">
    <span class="material-symbols-outlined {{ $iconClasses }} text-[22px]">{{ $icon }}</span>
    <span x-show="sidebarEffective === 'expanded'" x-transition.opacity.duration.150ms class="truncate">
        {{ $slot }}
    </span>
</a>
