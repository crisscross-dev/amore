@props([
    'label' => null,
    'name',
    'id' => null,
    'required' => false,
    'accept' => '',
])

@php($inputId = $id ?: $name)

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="file"
        name="{{ $name }}"
        id="{{ $inputId }}"
        @if($accept) accept="{{ $accept }}" @endif
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if($required) required @endif
    >
    
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
