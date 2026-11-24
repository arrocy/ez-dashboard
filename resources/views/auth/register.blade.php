@extends('layouts.auth')

@section('title', __('Create account'))

@section('content')
<h1 class="h3 fw-bold mb-2">@lang('Start your platform')</h1>
<p class="text-body-secondary mb-4">@lang('Create an account and build your WhatsApp automation business.')</p>
<x-auth.continue-with />

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <x-input name="name" label="Full name" required autocomplete="name" autofocus class="form-control-lg" />
    </div>
    <div class="mb-3">
        <x-input name="email" label="Email address" type="email" required autocomplete="email" class="form-control-lg" />
    </div>
    <div class="mb-3">
        <x-input name="password" label="Password" type="password" required autocomplete="new-password" class="form-control-lg" />
    </div>
    <div class="mb-4">
        <x-input name="password_confirmation" label="Confirm password" type="password" required autocomplete="new-password" class="form-control-lg" />
    </div>
    <x-button variant="dark" size="lg" type="submit" class="w-100">@lang('Create account')</x-button>
</form>
<p class="text-center text-body-secondary mt-4 mb-0">@lang('Already have an account?') <a href="{{ route('login') }}">@lang('Log in')</a></p>
@endsection
