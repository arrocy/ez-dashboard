<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <title>@yield('title', __('Account')) | {{ setting('company_name', 'Arrocy') }}</title>
    @include('partials.head')
</head>
<body>
    <main class="auth-shell d-flex align-items-center justify-content-center px-3 py-5">
        <div class="w-100">
            <a href="{{ route('welcome') }}" class="d-flex align-items-center justify-content-center gap-2 mb-4 text-dark fw-bold fs-4">
                <x-avatar :src="setting('company_avatar')" :name="setting('company_name', 'Arrocy')" />
                {{ setting('company_name', 'Arrocy') }}
            </a>
            <div class="auth-card bg-white p-4 p-md-5 mx-auto">
                @include('partials.notification')
                @yield('content')
            </div>
            <p class="text-center small text-body-secondary mt-4 mb-0">&copy;{{ now()->year }} {{ setting('company_name', 'Laravel') }} v{{ config('app.version', '1.0.0') }}. Built for better future.</p>
        </div>
    </main>
</body>
</html>
