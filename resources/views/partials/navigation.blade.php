<!-- Navigation -->
<nav class="bg-white shadow-sm sticky top-0 z-50 backdrop-blur-lg bg-white/95">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('img/Logo_BluePurple_Trans.png') }}" alt="DreamBet" class="h-12">
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}"
                    class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('Home') }}</a>
                <a href="{{ route('about') }}"
                    class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('About Us') }}</a>
                <a href="{{ route('contact') }}"
                    class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('Contact') }}</a>
                <a href="{{ route('terms-and-conditions') }}"
                    class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('Terms and Conditions') }}</a>
            </div>

            <!-- Right Side -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-gray-700 hover:text-blue-600 font-medium transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="text-gray-700 hover:text-blue-600 font-medium transition">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}"
                        class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition shadow-md">{{ __('Register') }}</a>
                @endauth

                <!-- Language Switcher -->
                <div class="relative group">
                    <button
                        class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
                            </path>
                        </svg>
                        <span class="uppercase font-semibold">{{ app()->getLocale() }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-36 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all border border-gray-200">
                        <a href="{{ route('lang.switch', 'en') }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 rounded-t-lg transition-colors {{ app()->getLocale() === 'en' ? 'bg-blue-50 font-semibold' : '' }}">
                            <span class="mr-2">🇺🇸</span>
                            {{ __('English') }}
                        </a>
                        <a href="{{ route('lang.switch', 'es') }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-colors {{ app()->getLocale() === 'es' ? 'bg-blue-50 font-semibold' : '' }}">
                            <span class="mr-2">🇪🇸</span>
                            {{ __('Spanish') }}
                        </a>
                        <a href="{{ route('lang.switch', 'fr') }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 rounded-b-lg transition-colors {{ app()->getLocale() === 'fr' ? 'bg-blue-50 font-semibold' : '' }}">
                            <span class="mr-2">🇫🇷</span>
                            {{ __('French') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-gray-700 hover:text-blue-600" id="mobile-menu-button">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="md:hidden hidden" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t">
            <a href="{{ route('home') }}"
                class="block px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-md">{{ __('Home') }}</a>
            <a href="{{ route('about') }}"
                class="block px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-md">{{ __('About Us') }}</a>
            <a href="{{ route('contact') }}"
                class="block px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-md">{{ __('Contact') }}</a>
            <a href="{{ route('terms-and-conditions') }}"
                class="block px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-md">{{ __('Terms and Conditions') }}</a>
            <div class="border-t pt-2 mt-2">
                <a href="{{ route('lang.switch', 'en') }}"
                    class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 rounded-md {{ app()->getLocale() === 'en' ? 'bg-blue-50 font-semibold' : '' }}">
                    <span class="mr-2">🇺🇸</span>
                    {{ __('English') }}
                </a>
                <a href="{{ route('lang.switch', 'es') }}"
                    class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 rounded-md {{ app()->getLocale() === 'es' ? 'bg-blue-50 font-semibold' : '' }}">
                    <span class="mr-2">🇪🇸</span>
                    {{ __('Spanish') }}
                </a>
                <a href="{{ route('lang.switch', 'fr') }}"
                    class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 rounded-md {{ app()->getLocale() === 'fr' ? 'bg-blue-50 font-semibold' : '' }}">
                    <span class="mr-2">🇫🇷</span>
                    {{ __('French') }}
                </a>
            </div>
            @auth
                <a href="{{ url('/dashboard') }}"
                    class="block px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-md">Dashboard</a>
            @else
                <a href="{{ route('login') }}"
                    class="block px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-md">{{ __('Login') }}</a>
                <a href="{{ route('register') }}"
                    class="block px-3 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md text-center font-semibold">{{ __('Register') }}</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function () {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>