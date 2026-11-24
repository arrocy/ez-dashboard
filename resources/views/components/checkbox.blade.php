@props([
    'name',
    'label' => null,
    'value' => 1,
    'checked' => false,
    'type' => 'checkbox',
])

@php
    $label = Str::headline($label ?? $name ?? '');
    $hasError = $errors->has($name);
@endphp

<div>
    <div class="form-check">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            @checked(old($name, $checked))
            {{ $attributes->class([
                'form-check-input',
                'is-invalid' => $hasError,
            ]) }}>
        @if ($label)
            <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
        @endif
    </div>
    @if ($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
    @endif
</div>
