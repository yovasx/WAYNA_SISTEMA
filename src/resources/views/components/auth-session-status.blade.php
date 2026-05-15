@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'wayna-alert-success font-medium']) }}>
        {{ $status }}
    </div>
@endif
