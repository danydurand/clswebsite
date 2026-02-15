<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'DreamBet' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Outfit', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-gray-50">
    @if(!request()->has('source') || request('source') !== 'register')
        @include('partials.navigation')
    @endif

    <!-- Main Content -->
    {{ $slot }}

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <img src="{{ asset('img/Logo_BluePurple_Trans.png') }}" alt="DreamBet" class="h-10 mb-4">
                    <p class="text-gray-400">{{ __('Your trusted platform for Lottery, Sports, and Casino') }}</p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">{{ __('Quick Links') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('about') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('contact') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('Contact') }}</a></li>
                        <li><a href="{{ route('terms-and-conditions') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('Terms and Conditions') }}</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">{{ __('Lotteries') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('register') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('New York Lottery') }}</a></li>
                        <li><a href="{{ route('register') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('Florida Lottery') }}</a></li>
                        <li><a href="{{ route('register') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('Georgia Lottery') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">{{ __('Language') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('lang.switch', 'en') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('English') }}</a></li>
                        <li><a href="{{ route('lang.switch', 'es') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('Spanish') }}</a></li>
                        <li><a href="{{ route('lang.switch', 'fr') }}"
                                class="text-gray-400 hover:text-white transition">{{ __('French') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} DreamBet. {{ __('All rights reserved') }}.</p>
            </div>
        </div>
    </footer>
</body>

</html>