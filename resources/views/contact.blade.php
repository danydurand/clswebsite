<x-layouts.public>
    <!-- Hero Header -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('Get In Touch') }}</h1>
            <p class="text-xl text-blue-100">
                {{ __('We\'d love to hear from you! Send us a message and we\'ll respond as soon as possible.') }}</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12">
                <!-- Contact Form -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <!-- Success/Error Messages -->
                        @if(session('success'))
                            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ session('success') }}
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ session('error') }}
                                </div>
                            </div>
                        @endif

                        <!-- Header -->
                        <div class="mb-8">
                            <h2
                                class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                                {{ __('Send us a Message') }}
                            </h2>
                            <p class="text-gray-600">{{ __('Fill out the form below and we\'ll get back to you shortly.') }}
                            </p>
                        </div>

                        <!-- Contact Form -->
                        <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                            @csrf

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('Your Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                                    placeholder="{{ __('John Doe') }}" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('Email Address') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror"
                                    placeholder="email@example.com" required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('Subject') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('subject') border-red-500 @enderror"
                                    placeholder="{{ __('How can we help you?') }}" required>
                                @error('subject')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('Message') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea id="message" name="message" rows="6"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('message') border-red-500 @enderror"
                                    placeholder="{{ __('Tell us more about your inquiry...') }}"
                                    required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ __('Send Message') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contact Information Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-8 sticky top-24">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Contact Information') }}</h3>

                        <div class="space-y-6">
                            <!-- Email -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-1">{{ __('Email') }}</h4>
                                    <a href="mailto:support@dreambet.ht"
                                        class="text-blue-600 hover:text-purple-600 transition-colors">
                                        support@dreambet.ht
                                    </a>
                                </div>
                            </div>

                            <!-- Support Hours -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-1">{{ __('Support Hours') }}</h4>
                                    <p class="text-gray-600 text-sm">{{ __('Monday - Friday') }}</p>
                                    <p class="text-gray-600 text-sm">9:00 AM - 6:00 PM EST</p>
                                </div>
                            </div>

                            <!-- Response Time -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-1">{{ __('Response Time') }}</h4>
                                    <p class="text-gray-600 text-sm">{{ __('We typically respond within 24 hours') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                {{ __('For urgent matters, please log in to your account and use the live chat feature.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>