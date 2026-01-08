<div>
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('Frequently Asked Questions') }}</h1>
            <p class="text-xl text-blue-100">{{ __('Find answers to common questions about our platform') }}</p>
        </div>
    </section>

    <!-- FAQs Content with Accordion -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(count($faqs) > 0)
                <!-- Single Column Accordion -->
                <div class="max-w-4xl mx-auto space-y-4">
                    @foreach($faqs as $index => $faq)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow"
                            x-data="{ open_{{ $index }}: false }">
                            
                            <!-- Accordion Header -->
                            <button @click="open_{{ $index }} = !open_{{ $index }}"
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-4 flex-1">
                                    <!-- Question Icon -->
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold shrink-0">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" />
                                        </svg>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-base md:text-lg font-bold text-gray-900 pr-2">
                                        {{ $faq->title }}
                                    </h3>
                                </div>

                                <!-- Chevron Icon -->
                                <svg class="w-6 h-6 text-gray-500 transition-transform duration-300 shrink-0"
                                    :class="{ 'rotate-180': open_{{ $index }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>

                            <!-- Accordion Content -->
                            <div x-show="open_{{ $index }}" x-collapse class="border-t border-gray-200">
                                <div class="p-6 bg-gradient-to-r from-blue-50/30 to-purple-50/30">
                                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                                        {!! str($faq->content)->markdown() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Help Section -->
                <div class="mt-12 bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="max-w-4xl mx-auto">
                        <svg class="w-12 h-12 mx-auto mb-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z" />
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Still have questions?') }}</h3>
                        <p class="text-gray-600 mb-6">
                            {{ __('Can\'t find the answer you\'re looking for? Please contact our support team.') }}</p>
                        <a href="{{ route('contact') }}"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                            </svg>
                            {{ __('Contact Support') }}
                        </a>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" />
                    </svg>
                    <p class="text-xl text-gray-600">
                        {{ __('No FAQs available at this time.') }}
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>