<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <header class="bg-white shadow">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="font-semibold text-lg">{{ config('app.name') }}</a>
                    <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Products') }}</a>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">{{ __('My area') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">{{ __('Log Out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900">{{ __('Register') }}</a>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>
    </body>
</html>
