@extends('layouts.main')

@section('title', __('Settings'))
@section('page-heading', __('Settings overview'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">@lang('Settings')</h1>
        <p class="text-body-secondary mb-0">@lang('Search, organize, and manage settings across your platform.')</p>
    </div>
    <x-button variant="dark" type="button" data-bs-toggle="modal" data-bs-target="#createRecordModal">
        <i class="fa-solid fa-plus me-2"></i>@lang('Add New Setting')
    </x-button>
</div>

<x-card>
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <form action="{{ route('setting.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <x-input name="search" id="search" type="search" label="" :value="request('search')" icon="fa-solid fa-magnifying-glass text-body-secondary" placeholder="Search settings..." aria-label="Search settings" />
            </div>
            <x-button variant="secondary" outline type="submit">@lang('Search')</x-button>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('setting.export') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-file-export me-2"></i>@lang('Export')</a>
        </div>
    </div>

    <form action="{{ route('setting.bulk-destroy') }}" method="POST" data-bulk-delete-form>
        @csrf
        @method('DELETE')
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small text-body-secondary">{{ $settings->total() }} @lang('settings found')</span>
            <x-button variant="danger" outline size="sm" type="submit" data-bulk-delete-button hidden><i class="fa-solid fa-trash-can me-1"></i>@lang('Bulk Delete')</x-button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th scope="col" style="width:2.5rem"><input class="form-check-input" type="checkbox" aria-label="Select all rows" data-select-all="input[name='ids[]']"></th>
                        <th scope="col">Key</th>
                        <th scope="col">Value</th>
                        <th scope="col" class="text-end">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($settings as $setting)
                        @php
                            $recordId = $setting->getKey();
                        @endphp
                        <tr>
                            <td><input class="form-check-input" type="checkbox" name="ids[]" value="{{ $recordId }}" aria-label="Select row"></td>
                            <td>{{ $setting->key }}</td>
                            <td class="text-body-secondary">{{ substr($setting->value, 0, 40) }}{{ strlen($setting->value) > 40 ? '...' : '' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('setting.edit', $setting) }}" class="btn btn-sm btn-outline-primary" title="Edit setting" aria-label="Edit setting">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if (! in_array($setting->key, $defaultKeys, true))
                                <x-button variant="danger" outline size="sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteRecordModal" :data-delete-url="route('setting.destroy', $setting)" :data-delete-name="$setting->key" title="Delete setting" aria-label="Delete setting">
                                    <i class="fa-solid fa-trash-can"></i>
                                </x-button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="No settings found" description="Try another search or add a new setting." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border-top pt-3 mt-2">
            <span class="small text-body-secondary">@lang('Showing') {{ $settings->firstItem() ?? 0 }}–{{ $settings->lastItem() ?? 0 }} @lang('of') {{ $settings->total() }}</span>
            {{ $settings->links('pagination::bootstrap-5') }}
        </div>
    </form>

    <x-modal id="deleteRecordModal" title="Delete Setting?" data-delete-modal>
        <form id="delete-setting-form" method="POST" action="{{ route('setting.index') }}" data-delete-form>
            @csrf
            @method('DELETE')
            @lang('Are you sure you want to delete setting') <strong data-delete-name></strong>? @lang('This action cannot be undone.')
        </form>
        <x-slot:footer>
            <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
            <x-button variant="danger" type="submit" form="delete-setting-form">@lang('Delete setting')</x-button>
        </x-slot:footer>
    </x-modal>
</x-card>

<x-modal id="createRecordModal" title="Add New Setting">
    <form id="create-setting-form" method="POST" action="{{ route('setting.store') }}">
        @csrf
        <div class="mb-3">
            <x-input name="key" id="new-key" icon="fa-solid fa-gear" required />
        </div>
    </form>
    <x-slot:footer>
        <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
        <x-button variant="dark" type="submit" form="create-setting-form">@lang('Create setting')</x-button>
    </x-slot:footer>
</x-modal>
@endsection
