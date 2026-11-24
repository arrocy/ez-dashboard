@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
    'hint' => null,
    'multiple' => false,
])

@php
    $label = Str::headline($label ?? $name ?? '');
    $hasError = $errors->has($name);
@endphp

<div>
    @if ($label)
        <label for="{{ $id ?? $name }}" class="form-label">
            {{ $label }}@if ($required)<span class="text-danger">*</span>@endif
        </label>
    @endif
    <select
        name="{{ $multiple ? $name.'[]' : $name }}"
        id="{{ $id ?? $name }}"
        @if ($required) required @endif
        @if ($multiple) multiple @endif
        {{ $attributes->class(['form-select', 'is-invalid' => $hasError]) }}>
        {{ $slot }}
    </select>
    @if ($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
    @elseif ($hint)
        <div class="form-text text-warning">{{ $hint }}</div>
    @endif
</div>

@if ($multiple)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const element = document.getElementById('{{ $id }}');
        if (element && window.Choices) {
            new window.Choices(element, {
                removeItemButton: true,
                searchEnabled: true,
                paste: false,
                duplicateItemsAllowed: false,
                // Matches standard Bootstrap 5 structural heights
                itemSelectText: '', 
            });
        }
    });
</script>
@endpush
@endif