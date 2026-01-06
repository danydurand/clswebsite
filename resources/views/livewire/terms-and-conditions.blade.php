<div>
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('Terms and Conditions') }}</h1>
            <p class="text-xl text-blue-100">{{ __('Please read our terms and conditions carefully') }}</p>
        </div>
    </section>

    <div class="flex justify-center mt-8 relative z-10">
        <a href="{{ route('register') }}"
            class="bg-white text-blue-600 px-10 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
            {{ __('Register Now') }}
        </a>
    </div>

    <!-- Terms Content -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(count($termsBySystem) > 0)
                <!-- Three Column Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($termsBySystem as $systemData)
                        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                            <!-- System Section Header -->
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-2 pb-3 border-b-2 border-blue-600">
                                    {{ $systemData['system']->name }}
                                </h2>
                            </div>

                            <!-- Terms for this system -->
                            <div class="space-y-4">
                                @foreach($systemData['terms'] as $term)
                                    <div class="pl-4 border-l-2 border-blue-200">
                                        <div class="text-sm text-gray-700 leading-relaxed prose prose-sm max-w-none">
                                            {!! str($term->text)->markdown() !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                    </svg>
                    <p class="text-xl text-gray-600">
                        {{ __('No terms and conditions available at this time.') }}
                    </p>
                </div>
            @endif

            <!-- Back to Register Link -->
            {{-- <div class="mt-12 text-center">
                <a href="{{ route('register') }}"
                    class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Register') }}
                </a>
            </div> --}}
        </div>
    </section>
</div>