<div class="w-full">
    {{-- Flash Messages --}}
    @include('components.flash-message')

    {{-- Header --}}
    <x-ui.list-header :title="__('Dashboard')" :subtitle="__('Your lottery ticket statistics')" gradientFrom="blue-600"
        gradientTo="purple-600" subtitleColor="blue-100" decorColor1="blue-500" decorColor2="purple-500" />

    <!-- Statistics Cards -->
    <div class="grid gap-6 md:grid-cols-3">
        <!-- Total Tickets Card -->
        <div
            class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 shadow-lg transition-all hover:shadow-xl hover:scale-105">
            <div class="relative z-10">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22 10V6c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2zm-2-1.46c-1.19.69-2 1.99-2 3.46s.81 2.77 2 3.46V18H4v-2.54c1.19-.69 2-1.99 2-3.46 0-1.48-.8-2.77-1.99-3.46L4 6h16v2.54z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-blue-100">{{ __('Total Tickets') }}</p>
                    <p class="text-4xl font-bold text-white">{{ number_format($totalTickets) }}</p>
                </div>
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-br from-blue-700 to-blue-800 opacity-0 transition-opacity group-hover:opacity-100">
            </div>
        </div>

        <!-- Winner Tickets Card -->
        <div
            class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 shadow-lg transition-all hover:shadow-xl hover:scale-105">
            <div class="relative z-10">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-green-100">{{ __('Winner Tickets') }}</p>
                    <p class="text-4xl font-bold text-white">{{ number_format($winnerTickets) }}</p>
                </div>
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-br from-green-600 to-emerald-700 opacity-0 transition-opacity group-hover:opacity-100">
            </div>
        </div>

        <!-- Loser Tickets Card -->
        <div
            class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-red-500 to-pink-600 p-6 shadow-lg transition-all hover:shadow-xl hover:scale-105">
            <div class="relative z-10">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-red-100">{{ __('Loser Tickets') }}</p>
                    <p class="text-4xl font-bold text-white">{{ number_format($loserTickets) }}</p>
                </div>
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-br from-red-600 to-pink-700 opacity-0 transition-opacity group-hover:opacity-100">
            </div>
        </div>
    </div>
</div>