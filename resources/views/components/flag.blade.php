@props(['code' => '', 'size' => 'md'])

@php
    $dimensions = [
        'xs' => '1rem',
        'sm' => '1.5rem',
        'md' => '2rem',
        'lg' => '3rem',
        'xl' => '4rem',
    ][$size] ?? (\Str::endsWith($size, ['rem', 'px']) ? $size : '2.25rem');
@endphp

<span class="position-relative d-inline-flex flex-shrink-0">
    <img
        src="{{ asset('flags/1x1/' . $code . '.svg') }}"
        alt="{{ $code }}"
        style="width: {{ $dimensions }}; height: {{ $dimensions }};"
        {{ $attributes->class(['rounded-3 object-fit-cover']) }}>
</span>
