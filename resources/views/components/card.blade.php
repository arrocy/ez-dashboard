@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'noPadding' => false,
    'hover' => false,
])

@if ($hover)
    @once
        <style>
            .card-hover {
                transition: transform .2s ease, box-shadow .2s ease;
            }

            .card-hover:hover {
                transform: translateY(-.125rem);
                box-shadow: var(--bs-box-shadow) !important;
            }
        </style>
    @endonce
@endif

<div {{ $attributes->class([
    'card border-light-subtle shadow-sm',
    'card-hover' => $hover,
]) }}>
    @if ($title || isset($header) || isset($actions) || $subtitle)
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between gap-3 py-3 px-4">
            <div class="d-flex align-items-center gap-3">
                @if ($icon)
                    <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-3 bg-primary-subtle text-primary-emphasis" style="width: 2.5rem; height: 2.5rem;">
                        <i class="{{ $icon }}"></i>
                    </span>
                @endif
                <div>
                    @if ($header ?? $title)
                        <h5 class="card-title mb-0 fw-semibold">{{ $header ?? $title }}</h5>
                    @endif
                    @if ($subtitle)
                        <p class="card-subtitle text-body-secondary small mt-1 mb-0">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @isset($actions)
                <div class="d-flex align-items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    <div class="card-body {{ $noPadding ? 'p-0' : 'p-4' }}">{{ $slot }}</div>
</div>
