<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Users Dashboard') - {{ config('app.name', 'InventoryOS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&amp;family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background font-body-sm antialiased h-screen flex overflow-hidden">
    <!-- SideNavBar -->
    @include('layouts.partials.sidebar')

    <!-- Main Content Wrapper -->
    <div class="ml-72 flex-1 flex flex-col h-full overflow-y-auto bg-background relative z-0">
        <!-- TopNavBar -->
        @include('layouts.partials.header')

        <!-- Canvas -->
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
