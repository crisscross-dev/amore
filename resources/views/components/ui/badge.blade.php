@props([
    'variant' => 'primary',
    'pill' => false,
])

@php
    $pillClass = $pill ? 'rounded-pill' : '';
@endphp

<span {{ $attributes->merge(['class' => "badge bg-{$variant} {$pillClass}"]) }}>
    {{ $slot }}
</span>
