<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <!-- Language Switcher -->
    <div class="absolute top-4 right-4 z-50">
        <div class="relative group">
            <button
                class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129">
                    </path>
                </svg>
                <span class="uppercase text-white font-semibold">{{ app()->getLocale() }}</span>
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
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

    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex overflow-hidden">
            <!-- Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-purple-600 to-purple-700"></div>

            <!-- Hero Image Overlay -->
            <div class="absolute inset-0 bg-cover bg-center opacity-30"
                style="background-image: url('{{ asset('img/login-hero.png') }}');"></div>

            <!-- Decorative Elements -->
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute bottom-0 left-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>

            <!-- Logo and Brand -->
            <a href="{{ route('home') }}"
                class="relative z-20 flex items-center text-2xl font-bold hover:opacity-90 transition-opacity"
                wire:navigate>
                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/10 backdrop-blur-sm mr-3">
                    <x-app-logo-icon class="h-8 fill-current text-white" />
                </span>
                <span class="text-white drop-shadow-lg">{{ config('app.name', 'DreamBet') }}</span>
            </a>

            <!-- Main Content -->
            <div class="relative z-20 mt-auto space-y-6">
                <div class="space-y-4">
                    <h2 class="text-4xl font-bold leading-tight drop-shadow-lg">
                        Welcome to DreamBet
                    </h2>
                    <p class="text-xl text-blue-100 leading-relaxed">
                        Your premier destination for Lottery, Sports Betting, and Casino games.
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-3 pt-4">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-white/90">Exciting Lottery Draws</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-white/90">Live Sports Betting</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-white/90">Premium Casino Games</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[400px]">
                <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden mb-4"
                    wire:navigate>
                    <span
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-purple-600">
                        <x-app-logo-icon class="size-8 fill-current text-white" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>