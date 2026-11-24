@extends('layouts.auth')

@section('title', __('Log in'))

@section('content')
<h1 class="h3 fw-bold mb-2">@lang('Welcome back')</h1>
<p class="text-body-secondary mb-4">@lang('Log in to manage your workspace.')</p>
<x-auth.continue-with />

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <x-input name="email" label="Email address" type="email" required autocomplete="username" autofocus class="form-control-lg" />
    </div>
    <div class="mb-3">
        <x-input name="password" type="password" required autocomplete="current-password" class="form-control-lg" />
    </div>
    <div class="text-end mb-3">
        <a href="{{ route('password.request') }}">@lang('Forgot password')?</a>
    </div>
    <div class="mb-4">
        <x-checkbox name="remember" value="1" label="Remember me" />
    </div>
    <x-button variant="dark" size="lg" type="submit" class="w-100">@lang('Log in')</x-button>
</form>
<p class="text-center text-body-secondary mt-4 mb-0">@lang('New to :company?', ['company' => setting('company_name', 'Arrocy')]) <a href="{{ route('register') }}">@lang('Create an account')</a></p>
@endsection
