<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DreamBet - {{ __('Win Big with DreamBet') }}</title>

    <!-- Fonts -->
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
    @include('partials.navigation')

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 text-white overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="absolute inset-0"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in">
                    {{ __('Win Big with DreamBet') }}
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto">
                    {{ __('Your trusted platform for Lottery, Sports, and Casino') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}"
                        class="bg-white text-blue-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        {{ __('Register Now') }}
                    </a>
                    <a href="#how-it-works"
                        class="bg-blue-700/50 backdrop-blur-sm text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-700/70 transition border-2 border-white/30">
                        {{ __('Learn More') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Wave SVG -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                    fill="#F9FAFB" />
            </svg>
        </div>
    </section>

    <!-- Available Lotteries Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ __('Available Lotteries') }}</h2>
                <p class="text-xl text-gray-600">{{ __('Play your favorite state lotteries') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- New York -->
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                        <div class="text-white text-center">
                            <svg class="w-24 h-24 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                            </svg>
                            <p class="text-2xl font-bold">NY</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('New York Lottery') }}</h3>
                        <p class="text-gray-600 mb-4">Play the Empire State's favorite games</p>
                        <a href="{{ route('register') }}"
                            class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            {{ __('Play Now') }}
                        </a>
                    </div>
                </div>

                <!-- Florida -->
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center">
                        <div class="text-white text-center">
                            <svg class="w-24 h-24 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                            </svg>
                            <p class="text-2xl font-bold">FL</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Florida Lottery') }}</h3>
                        <p class="text-gray-600 mb-4">Sunshine State jackpots await</p>
                        <a href="{{ route('register') }}"
                            class="block w-full bg-orange-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                            {{ __('Play Now') }}
                        </a>
                    </div>
                </div>

                <!-- Georgia -->
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center">
                        <div class="text-white text-center">
                            <svg class="w-24 h-24 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                            </svg>
                            <p class="text-2xl font-bold">GA</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Georgia Lottery') }}</h3>
                        <p class="text-gray-600 mb-4">Peach State prizes and more</p>
                        <a href="{{ route('register') }}"
                            class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                            {{ __('Play Now') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Coming Soon -->
            <div class="mt-12 grid md:grid-cols-2 gap-8">
                <div
                    class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-xl p-8 text-center relative overflow-hidden">
                    <div
                        class="absolute top-4 right-4 bg-yellow-400 text-gray-900 px-4 py-1 rounded-full text-sm font-bold">
                        {{ __('Coming Soon') }}
                    </div>
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">{{ __('Sports Betting') }}</h3>
                    <p class="text-gray-600">Bet on your favorite teams and events</p>
                </div>

                <div
                    class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-xl p-8 text-center relative overflow-hidden">
                    <div
                        class="absolute top-4 right-4 bg-yellow-400 text-gray-900 px-4 py-1 rounded-full text-sm font-bold">
                        {{ __('Coming Soon') }}
                    </div>
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">{{ __('Casino Games') }}</h3>
                    <p class="text-gray-600">Slots, table games, and live dealers</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose DreamBet Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ __('Why Choose DreamBet') }}</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Secure & Licensed') }}</h3>
                    <p class="text-gray-600">{{ __('Your safety is our priority with licensed operations') }}</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Easy Withdrawals') }}</h3>
                    <p class="text-gray-600">{{ __('Get your winnings quickly and securely') }}</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('24/7 Support') }}</h3>
                    <p class="text-gray-600">{{ __('Our team is always here to help you') }}</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Multiple Payment Methods') }}</h3>
                    <p class="text-gray-600">{{ __('Choose from various secure payment options') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 md:py-24 bg-gradient-to-br from-blue-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ __('How It Works') }}</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div
                        class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Create Account') }}</h3>
                    <p class="text-gray-600">{{ __('Sign up in minutes with your details') }}</p>
                </div>

                <div class="text-center">
                    <div
                        class="w-20 h-20 bg-purple-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Choose Your Lottery') }}</h3>
                    <p class="text-gray-600">{{ __('Select from NY, FL, or GA lotteries') }}</p>
                </div>

                <div class="text-center">
                    <div
                        class="w-20 h-20 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Select Numbers') }}</h3>
                    <p class="text-gray-600">{{ __('Pick your lucky numbers or use quick pick') }}</p>
                </div>

                <div class="text-center">
                    <div
                        class="w-20 h-20 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Win Big') }}</h3>
                    <p class="text-gray-600">{{ __('Check results and claim your prizes') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-24 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-bold mb-6">{{ __('Ready to Start Winning?') }}</h2>
            <p class="text-xl mb-8 text-blue-100">{{ __('Join thousands of players and start your journey today') }}</p>
            <a href="{{ route('register') }}"
                class="inline-block bg-white text-blue-600 px-10 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                {{ __('Register Now') }}
            </a>
        </div>
    </section>

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

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>

</html>