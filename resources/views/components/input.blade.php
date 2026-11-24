@props([
    'name',
    'id' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'icon' => null,
    'hint' => null,
    'useOldInput' => true,
])

@php
    $label = Str::headline($label ?? $name ?? '');
    $hasError = $errors->has($name);
    $valueUrl = is_string($value) ? filter_var($value, FILTER_VALIDATE_URL) : false;
    $valuePath = $valueUrl ? (parse_url($valueUrl, PHP_URL_PATH) ?: '') : '';
    $isImageUrl = $valueUrl && in_array(strtolower(pathinfo($valuePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'svg'], true);
    $inputValue = $type === 'password' ? null : ($useOldInput ? old($name, $value) : $value);
@endphp

<div>
    @if ($label)
        <label for="{{ $id ?? $name }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-danger">*</span>@endif
        </label>
    @endif
    @if ($icon || $isImageUrl)
        <div class="input-group">
            <span class="input-group-text">
                @if ($isImageUrl)
                    <x-avatar :src="$valueUrl" size="sm" />
                @else
                    <i class="{{ $icon }}"></i>
                @endif
            </span>
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $id ?? $name }}"
                @if ($type !== 'password') value="{{ $inputValue }}" @endif
                @if ($required) required @endif
                {{ $attributes->class(['form-control', 'is-invalid' => $hasError]) }}>
        </div>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            @if ($type !== 'password') value="{{ $inputValue }}" @endif
            @if ($required) required @endif
            {{ $attributes->class(['form-control', 'is-invalid' => $hasError]) }}>
    @endif
    @if ($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
    @elseif ($hint)
        <div class="form-text text-warning">{{ $hint }}</div>
    @endif
</div>
