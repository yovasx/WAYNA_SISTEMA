@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-primary-400 bg-primary-50 ps-3 pe-4 py-2 text-start text-base font-medium text-primary-700 focus:border-primary-600 focus:bg-primary-100 focus:text-primary-800 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full border-l-4 border-transparent ps-3 pe-4 py-2 text-start text-base font-medium text-ink-soft hover:border-stroke-strong hover:bg-surface-soft hover:text-ink focus:border-stroke-strong focus:bg-surface-soft focus:text-ink focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
