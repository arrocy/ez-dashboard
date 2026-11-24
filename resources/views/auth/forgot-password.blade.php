@extends('layouts.auth')

@section('title', __('Forgot password'))

@section('content')
<h1 class="h3 fw-bold mb-2">@lang('Reset your password')</h1>
<p class="text-body-secondary mb-4">@lang('Enter your email and we will send you a secure reset link.')</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-4">
        <x-input name="email" label="Email address" type="email" required autocomplete="email" autofocus class="form-control-lg" />
    </div>
    <x-button variant="dark" size="lg" type="submit" class="w-100">@lang('Email password reset link')</x-button>
</form>
<p class="text-center mt-4 mb-0"><a href="{{ route('login') }}">@lang('Back to log in')</a></p>
@endsection
