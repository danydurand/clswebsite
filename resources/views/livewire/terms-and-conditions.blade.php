<div>
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($source === 'register')
                <!-- Back to Register Button -->
                <div class="mb-6">
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center gap-2 text-white hover:text-blue-100 transition-colors group">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="font-semibold">{{ __('Back to Register') }}</span>
                    </a>
                </div>
            @endif
            
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('Terms and Conditions') }}</h1>
            <p class="text-xl text-blue-100">{{ __('Please read our terms and conditions carefully') }}</p>
        </div>
    </section>

    <!-- Terms Content with Cards -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(count($terms) > 0)
                <!-- Cards Container -->
                <div class="max-w-4xl mx-auto space-y-6">
                    @foreach($terms as $index => $term)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6">
                                <div class="flex items-center gap-4">
                                    <!-- Number Badge -->
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm text-white font-bold text-lg shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    
                                    <!-- Title -->
                                    <h3 class="text-xl md:text-2xl font-bold text-white">
                                        {{ $term->title }}
                                    </h3>
                                </div>
                            </div>
                            
                            <!-- Card Content -->
                            <div class="p-6">
                                <div class="prose prose-sm md:prose-base max-w-none text-gray-700 leading-relaxed">
                                    {!! str($term->content)->markdown() !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                    </svg>
                    <p class="text-xl text-gray-600">
                        {{ __('No terms and conditions available at this time.') }}
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>