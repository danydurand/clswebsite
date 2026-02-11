<div class="w-full">
    {{-- Gradient Header --}}
    <div class="relative mb-8 overflow-hidden rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 p-8 shadow-lg">
        <div class="relative z-10">
            <flux:heading size="xl" level="1" class="text-white">{{ __('Deposits') }}</flux:heading>
            <flux:subheading size="lg" class="text-green-100">{{ __('Manage your account deposits') }}
            </flux:subheading>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-green-500 opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-emerald-500 opacity-20 blur-3xl"></div>
    </div>

    {{-- Session Messages --}}
    @session('success')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 p-4 text-white shadow-xl"
            role="alert">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
                <p class="font-medium">{{ $value }}</p>
            </div>
        </div>
    @endsession

    @session('error')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 rounded-lg bg-gradient-to-r from-red-500 to-pink-600 p-4 text-white shadow-xl"
            role="alert">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                </svg>
                <p class="font-medium">{{ $value }}</p>
            </div>
        </div>
    @endsession

    {{-- Action Buttons --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Automatic Deposit --}}
        <a href="{{ route('deposits.create') }}" wire:navigate
            class="group relative inline-flex overflow-hidden rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 p-6 text-left shadow-lg transition-all hover:shadow-xl hover:scale-105">
            <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                    <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="mb-1 text-lg font-bold text-white">{{ __('Automatic Deposit') }}</h3>
                    <p class="text-sm text-green-100">{{ __('Instant payment processing') }}</p>
                </div>
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-r from-green-700 to-emerald-700 opacity-0 transition-opacity group-hover:opacity-100">
            </div>
        </a>

        {{-- Manual Deposit --}}
        <a href="{{ route('deposits.manual') }}" wire:navigate
            class="group relative inline-flex overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-left shadow-lg transition-all hover:shadow-xl hover:scale-105">
            <div class="relative z-10 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                    <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="mb-1 text-lg font-bold text-white">{{ __('Manual Deposit') }}</h3>
                    <p class="text-sm text-blue-100">{{ __('Bank transfer or cash deposit') }}</p>
                </div>
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-r from-blue-700 to-indigo-700 opacity-0 transition-opacity group-hover:opacity-100">
            </div>
        </a>
    </div>

    {{-- Deposits Table --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <flux:table :paginate="$deposits">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                    wire:click="sort('created_at')">
                    {{ __('Date') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Gateway') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'amount'" :direction="$sortDirection" align="right"
                    wire:click="sort('amount')">
                    {{ __('Amount') }}
                </flux:table.column>
                <flux:table.column align="right">
                    {{ __('Charge') }}
                </flux:table.column>
                <flux:table.column align="right">
                    {{ __('Total') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" align="center"
                    wire:click="sort('status')">
                    {{ __('Status') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($deposits as $deposit)
                    <flux:table.row :key="$deposit->id">
                        <flux:table.cell>
                            <div class="text-sm">
                                <div class="font-medium">{{ $deposit->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white text-xs font-bold">
                                    {{ substr($deposit->gateway?->name ?? 'N/A', 0, 2) }}
                                </div>
                                <span class="font-medium">{{ $deposit->gateway?->name ?? __('Unknown') }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="text-right font-medium">
                            ${{ number_format($deposit->amount, 2) }}
                        </flux:table.cell>

                        <flux:table.cell class="text-right text-sm text-gray-600 dark:text-gray-400">
                            ${{ number_format($deposit->charge, 2) }}
                        </flux:table.cell>

                        <flux:table.cell class="text-right font-semibold">
                            ${{ number_format($deposit->final_amount, 2) }}
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <flux:badge size="sm" :color="$deposit?->status?->getColor()" inset="top bottom">
                                {{ $deposit?->status?->getLabel() }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8">
                            <div class="text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="font-medium">{{ __('No deposits yet') }}</p>
                                <p class="text-sm mt-1">{{ __('Create your first deposit to get started') }}</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>