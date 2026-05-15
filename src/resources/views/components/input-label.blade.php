@props(['value'])

<label {{ $attributes->merge(['class' => 'block wayna-label']) }}>
    {{ $value ?? $slot }}
</label>
