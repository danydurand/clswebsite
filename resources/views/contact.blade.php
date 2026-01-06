<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Contact') }} - DreamBet</title>

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
    <!-- Navigation (same as home) -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 backdrop-blur-lg bg-white/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/Logo_BluePurple_Trans.png') }}" alt="DreamBet" class="h-10">
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}"
                        class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('Home') }}</a>
                    <a href="{{ route('about') }}"
                        class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('About Us') }}</a>
                    <a href="{{ route('contact') }}" class="text-blue-600 font-medium">{{ __('Contact') }}</a>
                    <a href="{{ route('terms-and-conditions') }}"
                        class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('Terms and Conditions') }}</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative group">
                        <button
                            class="flex items-center space-x-1 text-gray-700 hover:text-blue-600 font-medium transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
                                </path>
                            </svg>
                            <span class="uppercase">{{ app()->getLocale() }}</span>
                        </button>
                        <div
                            class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                            <a href="{{ route('lang.switch', 'en') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded-t-lg">{{ __('English') }}</a>
                            <a href="{{ route('lang.switch', 'es') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">{{ __('Spanish') }}</a>
                            <a href="{{ route('lang.switch', 'fr') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded-b-lg">{{ __('French') }}</a>
                        </div>
                    </div>

                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="text-gray-700 hover:text-blue-600 font-medium transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('Login') }}</a>
                        <a href="{{ route('register') }}"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition shadow-md">{{ __('Register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <section class="py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">{{ __('Contact') }}</h1>
            <div class="prose prose-lg max-w-none">
                <p class="text-xl text-gray-600 mb-6">
                    We'd love to hear from you! Get in touch with our team.
                </p>
                <p class="text-gray-600 mb-4">
                    This page is currently under construction. We're working on creating a comprehensive contact form
                    and providing you with multiple ways to reach us.
                </p>
                <p class="text-gray-600 mb-4">
                    For immediate assistance, please use the support options available in your dashboard after logging
                    in.
                </p>
                <div class="mt-8">
                    <a href="{{ route('home') }}"
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        {{ __('Back to Home') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-400">
                <p>&copy; {{ date('Y') }} DreamBet. {{ __('All rights reserved') }}.</p>
            </div>
        </div>
    </footer>
</body>

</html>