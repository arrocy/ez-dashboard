@props([
    'variant' => 'secondary',
    'pill' => false,
])

@php
    $variants = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
    $variant = in_array($variant, $variants, true) ? $variant : 'secondary';
@endphp

<span {{ $attributes->class([
    'badge text-bg-'.$variant,
    'rounded-pill' => $pill,
]) }}>
    {{ $slot }}
</span>
