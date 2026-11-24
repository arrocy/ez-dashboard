@extends('layouts.auth')

@section('title', __('Choose a new password'))

@section('content')
<h1 class="h3 fw-bold mb-2">@lang('Choose a new password')</h1>
<p class="text-body-secondary mb-4">@lang('Set a new password for your account.')</p>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
        <x-input name="email" label="Email address" type="email" :value="request('email')" required autocomplete="email" autofocus class="form-control-lg" />
    </div>
    <div class="mb-3">
        <x-input name="password" label="New password" type="password" required autocomplete="new-password" class="form-control-lg" />
    </div>
    <div class="mb-4">
        <x-input name="password_confirmation" label="Confirm new password" type="password" required autocomplete="new-password" class="form-control-lg" />
    </div>
    <x-button variant="dark" size="lg" type="submit" class="w-100">@lang('Reset password')</x-button>
</form>
@endsection
