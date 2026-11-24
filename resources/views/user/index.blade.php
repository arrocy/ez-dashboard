@extends('layouts.main')

@section('title', __('Users'))
@section('page-heading', __('Users overview'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">@lang('Users')</h1>
        <p class="text-body-secondary mb-0">@lang('Search, organize, and manage users across your platform.')</p>
    </div>
    <x-button variant="dark" type="button" data-bs-toggle="modal" data-bs-target="#createRecordModal">
        <i class="fa-solid fa-plus me-2"></i>@lang('Add New User')
    </x-button>
</div>

<x-card>
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <form action="{{ route('user.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <x-input name="search" id="search" type="search" label="" :value="request('search')" icon="fa-solid fa-magnifying-glass text-body-secondary" placeholder="Search users..." aria-label="Search users" />
            </div>
            <x-button variant="secondary" outline type="submit">@lang('Search')</x-button>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('user.export') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-file-export me-2"></i>@lang('Export')</a>
        </div>
    </div>

    <form action="{{ route('user.bulk-destroy') }}" method="POST" data-bulk-delete-form>
        @csrf
        @method('DELETE')
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small text-body-secondary">{{ $users->total() }} @lang('users found')</span>
            <x-button variant="danger" outline size="sm" type="submit" data-bulk-delete-button hidden><i class="fa-solid fa-trash-can me-1"></i>@lang('Bulk Delete')</x-button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th scope="col" style="width:2.5rem"><input class="form-check-input" type="checkbox" aria-label="Select all rows" data-select-all="input[name='ids[]']"></th>
                        <th scope="col">@lang('Name')</th>
                        <th scope="col">@lang('Email')</th>
                        <th scope="col">@lang('Created')</th>
                        <th scope="col" class="text-end">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $recordId = $user->getKey();
                        @endphp
                        <tr>
                            <td>
                                @if ($user->id !== Auth::id())
                                <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $recordId }}" aria-label="Select row">
                                @endif
                            </td>
                            <td><div class="d-flex align-items-center gap-2"><x-avatar :src="$user->avatar" :name="$user->name" /><span class="fw-semibold">{{ $user->name }}</span></div></td>
                            <td>{{ $user->email }}</td>
                            <td class="text-body-secondary">{{ $user->created_at?->format('M j, Y') }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('user.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit user" aria-label="Edit user">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if (! auth()->user()->is($user))
                                    <a href="{{ route('impersonate', $user) }}" class="btn btn-sm btn-outline-dark" title="{{ __('Impersonate') }} {{ $user->name }}" aria-label="impersonate-{{ $user->name }}">
                                        <i class="fa-solid fa-user-secret" aria-hidden="true"></i>
                                    </a>
                                    <x-button variant="danger" outline size="sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteRecordModal" :data-delete-url="route('user.destroy', $user)" :data-delete-name="$user->name" title="Delete user" aria-label="Delete user">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </x-button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="No users found" description="Try another search or add a new user." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border-top pt-3 mt-2">
            <span class="small text-body-secondary">@lang('Showing') {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} @lang('of') {{ $users->total() }}</span>
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </form>
</x-card>

<x-modal id="deleteRecordModal" title="Delete User?" data-delete-modal>
    <form id="delete-user-form" method="POST" action="{{ route('user.index') }}" data-delete-form>
        @csrf
        @method('DELETE')
        @lang('Are you sure you want to delete') <strong data-delete-name></strong>? @lang('This action cannot be undone.')
    </form>
    <x-slot:footer>
        <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
        <x-button variant="danger" type="submit" form="delete-user-form">@lang('Delete user')</x-button>
    </x-slot:footer>
</x-modal>

<x-modal id="createRecordModal" title="Add New User">
    <form id="create-user-form" method="POST" action="{{ route('user.store') }}">
        @csrf
        <div class="mb-3">
            <x-input name="email" id="new-email" label="Email" type="email" icon="fa-solid fa-envelope" required />
        </div>
        <div class="mb-3">
            <x-input name="password" id="new-password" label="Password" type="password" icon="fa-solid fa-lock" required />
        </div>
    </form>
    <x-slot:footer>
        <x-button variant="light" type="button" data-bs-dismiss="modal">@lang('Cancel')</x-button>
        <x-button variant="dark" type="submit" form="create-user-form">@lang('Create user')</x-button>
    </x-slot:footer>
</x-modal>
@endsection
