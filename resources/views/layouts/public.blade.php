<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', setting('company_name', 'Arrocy'))</title>
    @include('partials.head')
</head>
<body>
    @include('partials.notification')
    @yield('content')
    @include('partials.footer')
    @stack('scripts')
</body>
</html>
