<div class="w-full">
    {{-- Gradient Header --}}
    <div class="relative mb-4 overflow-hidden rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 p-8 shadow-lg">
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
            class="group relative inline-flex overflow-hidden rounded-xl bg-blue-600 p-6 text-left shadow-lg transition-all hover:shadow-xl hover:scale-105">
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
            <div class="absolute inset-0 bg-blue-700 opacity-0 transition-opacity group-hover:opacity-100">
            </div>
        </a>
    </div>

    {{-- Filters Section (Collapsible) --}}
    <div x-data="{ filtersOpen: false }"
        class="mb-6 rounded-xl bg-white shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        {{-- Toggle Button --}}
        <button @click="filtersOpen = !filtersOpen"
            class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors rounded-xl">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Filters') }}</span>
                @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $gatewayFilter !== 'all')
                    <span
                        class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-200">
                        {{ __('Active') }}
                    </span>
                @endif
            </div>
            <svg class="h-5 w-5 text-gray-600 dark:text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180': filtersOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Filters Content --}}
        <div x-show="filtersOpen" x-collapse class="border-t border-gray-200 dark:border-zinc-700">
            <div class="p-6">
                {{-- First Row: All Filters --}}
                <div class="grid grid-cols-5 gap-4 mb-4">
                    {{-- Status Filter --}}
                    <div>
                        <flux:label>{{ __('Filter by Status') }}</flux:label>
                        <flux:select wire:model.live="statusFilter">
                            <option value="all">{{ __('All Statuses') }}</option>
                            <option value="initiate">{{ __('Initiate') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="success">{{ __('Success') }}</option>
                            <option value="reject">{{ __('Reject') }}</option>
                        </flux:select>
                    </div>

                    {{-- Gateway Filter --}}
                    <div>
                        <flux:label>{{ __('Filter by Gateway') }}</flux:label>
                        <flux:select wire:model.live="gatewayFilter">
                            <option value="all">{{ __('All Gateways') }}</option>
                            @foreach($this->usedGateways as $gateway)
                                <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Transaction Code Search --}}
                    <div>
                        <flux:label>{{ __('Search by Transaction Code') }}</flux:label>
                        <flux:input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Enter transaction code...') }}" icon="magnifying-glass" />
                    </div>

                    {{-- Date From --}}
                    <div>
                        <flux:label>{{ __('Date From') }}</flux:label>
                        <flux:input type="date" wire:model.live="dateFrom" />
                    </div>

                    {{-- Date To --}}
                    <div>
                        <flux:label>{{ __('Date To') }}</flux:label>
                        <flux:input type="date" wire:model.live="dateTo" />
                    </div>

                </div>

                {{-- Second Row: Clear Filters Button (Centered) --}}
                @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $gatewayFilter !== 'all')
                    <div class="flex justify-center">
                        <button wire:click="clearFilters"
                            class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2.5 text-white font-medium transition-colors hover:bg-gray-700">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ __('Clear Filters') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
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
                <flux:table.column align="center">
                    {{ __('Actions') }}
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

                        <flux:table.cell align="center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- View Button --}}
                                <a href="{{ route('deposits.view', $deposit->id) }}" wire:navigate
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                    title="{{ __('View') }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                {{-- Edit Button (disabled for non-PENDING deposits) --}}
                                @if($deposit->status === App\Domain\Deposit\DepositStatusEnum::Pending)
                                    <a href="{{ route('deposits.edit', $deposit->id) }}" wire:navigate
                                        class="inline-flex items-center justify-center rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-green-700"
                                        title="{{ __('Edit') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @else
                                    <button disabled
                                        class="inline-flex items-center justify-center rounded-lg bg-gray-400 px-3 py-1.5 text-sm font-medium text-white cursor-not-allowed opacity-50"
                                        title="{{ __('Cannot edit - Deposit is not pending') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8">
                            <div class="text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $gatewayFilter !== 'all')
                                    <p class="font-medium">{{ __('No deposits found matching your filters') }}</p>
                                    <button wire:click="clearFilters" class="mt-2 text-sm text-blue-600 hover:underline">
                                        {{ __('Clear filters to see all deposits') }}
                                    </button>
                                @else
                                    <p class="font-medium">{{ __('No deposits yet') }}</p>
                                    <p class="text-sm mt-1">{{ __('Create your first deposit to get started') }}</p>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>