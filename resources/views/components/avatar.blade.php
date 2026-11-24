@props(['name' => '', 'src' => null, 'size' => 'md', 'status' => null, 'id' => null])

@php
    $initials = collect(explode(' ', trim($name)))->filter()->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('');
    $dimensions = [
        'xs' => '1rem',
        'sm' => '1.5rem',
        'md' => '2rem',
        'lg' => '3rem',
        'xl' => '4rem',
    ][$size] ?? (\Str::endsWith($size, ['rem', 'px']) ? $size : '2.25rem');
    $statusColor = [
        'online' => 'bg-success',
        'busy' => 'bg-warning',
        'offline' => 'bg-secondary',
    ][$status] ?? null;
@endphp

<span class="position-relative d-inline-flex flex-shrink-0">
    @if ($src)
        <img
            id="{{ $id }}"
            src="{{ $src }}"
            alt="{{ $name }}"
            style="width: {{ $dimensions }}; height: {{ $dimensions }};"
            {{ $attributes->class(['rounded-3 object-fit-cover']) }}>
    @else
        <span
            style="width: {{ $dimensions }}; height: {{ $dimensions }};"
            {{ $attributes->class(['d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary-emphasis small fw-semibold text-uppercase']) }}>
            {{ $initials ?: '?' }}
        </span>
    @endif
    @if ($statusColor)
        <span
            class="position-absolute bottom-0 end-0 rounded-circle border border-2 border-white {{ $statusColor }}"
            style="width: .75rem; height: .75rem;"
            role="img"
            aria-label="{{ ucfirst($status) }}">
        </span>
    @endif
</span>
