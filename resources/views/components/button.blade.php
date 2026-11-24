@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => null,
    'outline' => false,
    'disabled' => false,
])

@php
    $variants = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
    $buttonTypes = ['button', 'submit', 'reset'];
    $variant = in_array($variant, $variants, true) ? $variant : 'primary';
    $type = in_array($type, $buttonTypes, true) ? $type : 'button';
@endphp

<button
    type="{{ $type }}"
    @disabled($disabled)
    {{ $attributes->class([
        'btn',
        'btn-'.($outline ? 'outline-' : '').$variant,
        'btn-'.$size => in_array($size, ['sm', 'lg'], true),
    ]) }}>
    {{ $slot }}
</button>
