@props([
    'id',
    'title' => null,
    'size' => null,
    'centered' => true,
    'scrollable' => false,
    'staticBackdrop' => false,
    'showClose' => true,
])

@php
    $sizeClass = in_array($size, ['sm', 'lg', 'xl'], true) ? 'modal-'.$size : null;
@endphp

<div
    id="{{ $id }}"
    tabindex="-1"
    aria-hidden="true"
    @if ($title) aria-labelledby="{{ $id }}-title" @endif
    @if ($staticBackdrop) data-bs-backdrop="static" data-bs-keyboard="false" @endif
    {{ $attributes->class(['modal fade']) }}>
    <div class="modal-dialog {{ $sizeClass }} {{ $centered ? 'modal-dialog-centered' : '' }} {{ $scrollable ? 'modal-dialog-scrollable' : '' }}">
        <div class="modal-content">
            @if ($title || isset($header) || $showClose)
                <div class="modal-header">
                    @if (isset($header))
                        {{ $header }}
                    @elseif ($title)
                        <h2 class="modal-title fs-5" id="{{ $id }}-title">{{ $title }}</h2>
                    @endif
                    @if ($showClose)
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    @endif
                </div>
            @endif
            <div class="modal-body">{{ $slot }}</div>
            @isset($footer)
                <div class="modal-footer">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>
