@props([
    'label' => null,
    'name',
    'value' => '1',
    'checked' => false,
])

<div class="form-check mb-3">
    <input 
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => 'form-check-input' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        {{ old($name, $checked) ? 'checked' : '' }}
    >
    
    @if($label)
        <label class="form-check-label" for="{{ $name }}">
            {{ $label }}
        </label>
    @endif
    
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
