<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <title>@yield('title', setting('company_name', 'Arrocy'))</title>
    @include('partials.head')
</head>
<body>
    @include('partials.notification')
    @yield('content')
</body>
</html>
