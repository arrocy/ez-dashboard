<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <title>@yield('title', __('Dashboard')) | {{ setting('company_name', 'Arrocy') }}</title>
    @include('partials.head')
</head>
<body>
    <div class="dashboard-shell" data-dashboard-shell>
        @include('partials.sidebar')

        <div class="dashboard-main">
            @include('partials.navbar')

            <main class="container-fluid px-3 px-lg-4 py-4">
                @include('partials.notification')
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
