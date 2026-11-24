@extends('layouts.auth')

@section('title', __('Verify your email'))

@section('content')
<h1 class="h3 fw-bold mb-2">@lang('Verify your email')</h1>
<p class="text-body-secondary mb-4">@lang('We sent a verification link to your email address. Open it to unlock your workspace.')</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <x-button variant="dark" size="lg" type="submit" class="w-100">@lang('Resend verification email')</x-button>
</form>

<form method="POST" action="{{ route('logout') }}" class="text-center mt-3">
    @csrf
    <button class="btn btn-link" type="submit">@lang('Log out')</button>
</form>
@endsection
