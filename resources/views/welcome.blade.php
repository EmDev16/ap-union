<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">

    <header class="p-4 bg-white shadow flex justify-between">
        <a href="{{ url('/') }}" class="font-bold text-xl">
            {{ config('app.name') }}
        </a>

        <nav class="flex gap-4">
            @auth
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </nav>
    </header>

    <main class="p-8">
        <p>Connect with people more deeply.</p>
    </main>

</body>
</html>
