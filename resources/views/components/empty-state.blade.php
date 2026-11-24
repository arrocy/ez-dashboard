@props([
    'title',
    'description' => null,
    'icon' => 'fa-solid fa-inbox',
])

<div {{ $attributes->class(['text-center py-5 px-3']) }}>
    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-secondary-subtle text-secondary-emphasis mb-3" style="width: 3.5rem; height: 3.5rem;">
        <i class="{{ $icon }} fs-5" aria-hidden="true"></i>
    </span>
    <h2 class="h5 fw-semibold mb-2">{{ $title }}</h2>
    @if ($description)
        <p class="text-body-secondary mb-0">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-3">{{ $action }}</div>
    @endisset
</div>
