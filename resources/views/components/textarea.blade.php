@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'rows' => 3,
    'hint' => null,
])

@php
    $label = Str::headline($label ?? $name ?? '');
    $hasError = $errors->has($name);
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-danger">*</span>@endif
        </label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->class(['form-control', 'is-invalid' => $hasError]) }}>{{ old($name, $value) }}</textarea>
    @if ($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
    @elseif ($hint)
        <div class="form-text text-warning">{{ $hint }}</div>
    @endif
</div>
