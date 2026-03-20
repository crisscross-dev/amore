@props([
    'type' => 'info',
    'dismissible' => true,
])

@php
    $typeClasses = [
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];
    
    $alertClass = $typeClasses[$type] ?? 'alert-info';
@endphp

<div {{ $attributes->merge(['class' => 'alert ' . $alertClass . ($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
    {{ $slot }}
    
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
