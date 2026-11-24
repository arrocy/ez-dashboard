<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="description" content="{{ setting('company_description', 'Application description') }}">
<meta name="keywords" content="Laravel admin template, Laravel 13, admin dashboard">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="{{ setting('company_avatar', asset('img/favicon.png')) }}" />
<script>
    const savedTheme = localStorage.getItem('dashboard-theme');
    if (savedTheme === 'dark' || savedTheme === 'light') {
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    }
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
