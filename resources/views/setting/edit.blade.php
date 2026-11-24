@extends('layouts.main')

@section('title', __('Edit Setting'))
@section('page-heading', __('Edit Setting'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">@lang('Edit Setting')</h1>
        <p class="text-body-secondary mb-0">{{ $setting->key }}</p>
    </div>
</div>

<x-card>
    <form method="POST" action="{{ route('setting.update', $setting) }}">
        @csrf
        @method('PUT')
        <div class="row row-cols-md-2 g-3 py-4">
            <x-input name="key" id="edit-key" :value="$setting->key" icon="fa-solid fa-gear" readonly="{{ in_array($setting->key, $defaultKeys, true) ? 'readonly' : '' }}" required />
            <x-input name="value" id="edit-value" :value="$setting->value" icon="fa-solid fa-sliders" />
        </div>
        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('setting.index') }}" class="btn btn-light">@lang('Cancel')</a>
            <x-button variant="dark" type="submit">@lang('Save changes')</x-button>
        </div>
    </form>
</x-card>
@endsection
