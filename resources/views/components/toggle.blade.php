@props([
    'name',
    'label' => null,
    'checked' => false,
    'value' => 1,
    'color' => 'primary',
    'submitOnChange' => false,
])

@php
    $label = Str::headline($label ?? $name ?? '');
    $toggleColor = [
        'primary' => 'primary',
        'green' => 'success',
        'accent' => 'success',
        'amber' => 'warning',
    ][$color] ?? 'primary';
@endphp

@once
    <style>
        .form-check-input[data-bs-toggle-color]:checked {
            background-color: var(--toggle-color);
            border-color: var(--toggle-color);
        }
    </style>
@endonce

<div class="form-check form-switch">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        role="switch"
        @checked(old($name, $checked))
        @if ($submitOnChange) onchange="if (this.form) this.form.requestSubmit()" @endif
        style="--toggle-color: var(--bs-{{ $toggleColor }})"
        data-bs-toggle-color
        aria-label="{{ $label ?: $name }}"
        {{ $attributes->class(['form-check-input']) }}>
    @if ($label)
        <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
    @endif
</div>
