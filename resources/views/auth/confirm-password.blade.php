@extends('layouts.auth')

@section('title', __('Confirm password'))

@section('content')
<h1 class="h3 fw-bold mb-2">@lang('Confirm your password')</h1>
<p class="text-body-secondary mb-4">@lang('For your security, please confirm your password before continuing.')</p>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div class="mb-4">
        <x-input name="password" label="Password" type="password" required autocomplete="current-password" autofocus class="form-control-lg" />
    </div>
    <x-button variant="dark" size="lg" type="submit" class="w-100">@lang('Confirm password')</x-button>
</form>
@endsection
