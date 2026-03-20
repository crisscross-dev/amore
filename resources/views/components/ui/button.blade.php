@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'fullWidth' => false,
    'href' => null,
])

@php
    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? '';
    $widthClass = $fullWidth ? 'w-100' : '';
    $btnClass = "btn btn-{$variant} {$sizeClass} {$widthClass}";
@endphp

@if($href)
    <a 
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $btnClass]) }}
    >
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $btnClass]) }}
    >
        {{ $slot }}
    </button>
@endif
