@php
    $types = ['success', 'danger', 'warning', 'info', 'primary', 'secondary', 'light', 'dark'];
@endphp

{{-- 1. Render standard Bootstrap/Custom Alerts --}}
@foreach ($types as $type)
    {{-- Check both the exact type or its common Laravel mapping alias --}}
    @if (session($type) || ($type === 'danger' && session('error')))
        <x-alert :type="$type" dismissible>
            {{ session($type) ?? session('error') }}
        </x-alert>
    @endif
@endforeach

{{-- 2. Render Validation Errors --}}
@if ($errors->any())
    <x-alert type="danger" dismissible>
        <div class="fw-semibold mb-1">Please review the following:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
