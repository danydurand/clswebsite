<x-layouts.public>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('About DreamBet') }}</h1>
            <p class="text-xl text-blue-100">{{ __('Your trusted partner in lottery, sports, and casino gaming') }}</p>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Mission -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h2
                            class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ __('Our Mission') }}
                        </h2>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('We are the leading company in the lottery and gaming market, combining capital, talent, and cutting-edge technology to offer high-quality service to all our clients. We create trust and promote growth and development through job creation and community contributions.') }}
                    </p>
                </div>

                <!-- Vision -->
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </div>
                        <h2
                            class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                            {{ __('Our Vision') }}
                        </h2>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('To maintain leadership in the lottery and gaming market, supported by continuous innovation of our processes, use of advanced technologies, and a foundation of excellence and customer satisfaction.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ __('Everything for Our Players') }}
                </h2>
                <p class="text-xl text-gray-600">
                    {{ __('We provide all the tools and services you need for the best gaming experience') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Secure Platform -->
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Secure Platform') }}</h3>
                        <p class="text-gray-600 mb-4">
                            {{ __('Your money and winnings are protected with state-of-the-art security. Deposit and start playing immediately.') }}
                        </p>
                        <a href="{{ route('register') }}"
                            class="text-blue-600 hover:text-purple-600 font-semibold transition">
                            {{ __('Get Started') }} →
                        </a>
                    </div>
                </div>

                <!-- Live Gaming -->
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Live Gaming') }}</h3>
                        <p class="text-gray-600 mb-4">
                            {{ __('Play lottery games and place bets on sporting events worldwide including Football, Basketball, Baseball, and more.') }}
                        </p>
                        <a href="{{ route('register') }}"
                            class="text-blue-600 hover:text-purple-600 font-semibold transition">
                            {{ __('View Games') }} →
                        </a>
                    </div>
                </div>

                <!-- 24/7 Support -->
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('24/7 Customer Support') }}</h3>
                        <p class="text-gray-600 mb-4">
                            {{ __('Our dedicated support team is always available to help you with any questions or concerns you may have.') }}
                        </p>
                        <a href="{{ route('contact') }}"
                            class="text-blue-600 hover:text-purple-600 font-semibold transition">
                            {{ __('Contact Us') }} →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ __('Our Core Values') }}</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Trust') }}</h3>
                    <p class="text-gray-600">
                        {{ __('Building lasting relationships through transparency and reliability') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Innovation') }}</h3>
                    <p class="text-gray-600">
                        {{ __('Continuously improving our platform with cutting-edge technology') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Community') }}</h3>
                    <p class="text-gray-600">{{ __('Supporting growth and development through job creation') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Excellence') }}</h3>
                    <p class="text-gray-600">{{ __('Committed to delivering the highest quality service') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-bold mb-6">{{ __('Ready to Join DreamBet?') }}</h2>
            <p class="text-xl mb-8 text-blue-100">
                {{ __('Start your journey with us today and experience the difference') }}</p>
            <a href="{{ route('register') }}"
                class="inline-block bg-white text-blue-600 px-10 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                {{ __('Create Your Account') }}
            </a>
        </div>
    </section>
</x-layouts.public>