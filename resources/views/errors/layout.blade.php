<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Request error') · {{ setting('company_name', 'Arrocy') }}</title>
    @vite('resources/css/app.css')
</head>
<body>
    @php
        $exceptionMessage = $exception?->getMessage();
        $errorMessage = config('app.debug') && filled($exceptionMessage)
            ? $exceptionMessage
            : trim($__env->yieldContent('fallbackMessage', __('Something went wrong. Please try again later.')));
    @endphp

    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <section class="surface-card bg-white p-4 p-md-5 mx-auto text-center" style="max-width: 38rem;" aria-labelledby="error-title">
            <div class="feature-icon mx-auto mb-4" aria-hidden="true">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <p class="text-uppercase small fw-semibold text-primary mb-2">@yield('code', __('Error'))</p>
            <h1 id="error-title" class="h2 mb-3">@yield('heading', __('We could not complete your request'))</h1>
            <p class="text-body-secondary mb-4">{{ $errorMessage }}</p>
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fa-solid fa-house me-2" aria-hidden="true"></i>
                @lang('Return to home')
            </a>
        </section>
    </main>
</body>
</html>
