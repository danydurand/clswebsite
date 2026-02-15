<div class="relative w-full mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <flux:heading size="xl" level="1" class="mb-2">{{ __('Events') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-green-600 text-white text-sm p-4 rounded shadow-lg" role="alert">
            <p>{{ $value }}</p>
        </div>
    @endsession()

    <!-- Coming Soon Card -->
    <div class="flex items-center justify-center py-8">
        <div class="w-full max-w-3xl">
            <!-- Image Container with Shadow and Rounded Corners -->
            <div class="relative overflow-hidden rounded-2xl shadow-2xl mb-6">
                <img src="{{ asset('images/coming-soon/sports.png') }}" alt="{{ __('Sports Betting Coming Soon') }}"
                    class="w-full object-cover" style="height: 400px;" />
            </div>
            <!-- Message Card -->
            <div
                class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-8 text-center border border-gray-200 dark:border-zinc-700">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <flux:heading size="lg" level="2" class="mb-4 text-gray-900 dark:text-white">
                    {{ __('Sports Betting Coming Soon') }}
                </flux:heading>
                <flux:text class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    {{ __("Get ready to bet on your favorite sports! Our sports betting platform will be available soon.") }}
                </flux:text>
            </div>
        </div>
    </div>
</div>