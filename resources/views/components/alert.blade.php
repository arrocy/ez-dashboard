@props([
    'type' => 'info',
    'title' => null,
    'icon' => null,
    'dismissible' => false,
])

@php
    $types = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
    $type = in_array($type, $types, true) ? $type : 'info';
    $defaultIcons = [
        'success' => 'fa-solid fa-circle-check',
        'danger' => 'fa-solid fa-circle-exclamation',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'info' => 'fa-solid fa-circle-info',
    ];
@endphp

<div
    role="alert"
    {{ $attributes->class(['alert alert-'.$type, 'alert-dismissible fade show' => $dismissible]) }}>

    @if ($icon || isset($defaultIcons[$type]))
        <i class="{{ $icon ?: $defaultIcons[$type] }} me-2" aria-hidden="true"></i>
    @endif

    <strong>{{ $title ?: ucfirst($type) }}</strong><br>

    {{ $slot }}

    @if ($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
