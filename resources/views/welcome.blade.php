<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="flex min-h-screen items-center justify-center bg-gray-100">
        <div class="rounded-xl bg-white p-8 shadow-lg">
            <h1 class="text-3xl font-medium">
                Log in to your account
            </h1>

            <p class="mt-2 text-gray-600">
                Tailwind CSS is working!
            </p>
        </div>
    </div>
</body>

</html>