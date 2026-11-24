@extends('layouts.main')

@section('title', __('Edit User'))
@section('page-heading', __('Edit User'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">@lang('Edit User')</h1>
        <p class="text-body-secondary mb-0">{{ $user->name }}</p>
    </div>
</div>

<x-card>
    <form id="edit-user-form" method="POST" action="{{ route('user.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="row row-cols-md-2 g-3 py-4">
            <x-input name="name" id="edit-name" label="Name" :value="$user->name" icon="fa-solid fa-user" required />
            <x-input name="email" id="edit-email" label="Email" type="email" :value="$user->email" icon="fa-solid fa-envelope" required />
            <x-input name="phone" id="edit-phone" label="Phone" :value="$user->phone" icon="fa-solid fa-phone" />
            <x-select name="role" id="edit-role" label="Role" required>
                @foreach (config('rbac.roles') as $role)
                <option value="{{ $role }}" @selected($role === $user->role)>{{ $role }}</option>
                @endforeach
            </x-select>
            <x-select name="status" id="edit-status" label="Status">
                <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                <option value="suspended" @selected(old('status', $user->status) === 'suspended')>Suspended</option>
            </x-select>
            <x-select name="timezone" id="edit-timezone" label="Timezone">
                @foreach (config('app.timezones') as $key => $value)
                <option value="{{ $key }}" @selected(old('timezone', $user->timezone) === $key)>{{ $key }} - {{ $value }}</option>
                @endforeach
            </x-select>
            <x-input name="avatar" id="edit-avatar" label="Avatar" :value="$user->avatar" icon="fa-solid fa-image" />
            <x-input name="email_verified_at" id="edit-email-verified-at" label="Email Verified At" type="datetime-local" :value="$user->email_verified_at?->format('Y-m-d\TH:i')" />
            <x-input name="password" id="edit-password" label="New password (optional)" type="password" autocomplete="new-password" icon="fa-solid fa-lock" />
            <x-input name="password_confirmation" id="edit-password-confirmation" label="Confirm new password" type="password" autocomplete="new-password" icon="fa-solid fa-lock" />
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('user.index') }}" class="btn btn-light">@lang('Cancel')</a>
            <x-button variant="dark" type="submit">@lang('Save changes')</x-button>
        </div>
    </form>
</x-card>
@endsection
