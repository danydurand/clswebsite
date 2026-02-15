<div class="space-y-6">
    {{-- Flash Messages --}}
    @include('components.flash-message')

    {{-- Header --}}
    <x-ui.list-header :title="__('Withdrawals')" :subtitle="__('Manage your withdrawal requests')"
        gradientFrom="green-600" gradientTo="emerald-600" subtitleColor="green-100" decorColor1="green-500"
        decorColor2="emerald-500" />

    {{-- Filters Section (Collapsible) --}}
    <div x-data="{ filtersOpen: false }"
        class="mb-6 rounded-xl bg-white shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">

        {{-- Filter Toggle Button --}}
        <button @click="filtersOpen = !filtersOpen"
            class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors rounded-t-xl">
            <div class="flex items-center space-x-3">
                <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="font-semibold text-gray-900 dark:text-white">{{ __('Filters') }}</span>

                @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $methodFilter > 0)
                    <span
                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                        {{ __('Active') }}
                    </span>
                @endif
            </div>

            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400 transition-transform duration-200"
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
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Method Filter --}}
                    <div>
                        <flux:label>{{ __('Filter by Method') }}</flux:label>
                        <flux:select wire:model.live="methodFilter">
                            <option value="0">{{ __('All Methods') }}</option>
                            @foreach($methods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Search by Transaction Code --}}
                    <div>
                        <flux:label>{{ __('Search by Transaction Code') }}</flux:label>
                        <flux:input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Enter transaction code...') }}" />
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
                @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $methodFilter > 0)
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

    {{-- Withdrawals Table --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <flux:table :paginate="$withdrawals">
            <flux:table.columns>
                {{-- Date --}}
                <flux:table.column sortable :sorted="$sortBy === 'created_at' ? $sortDirection : null"
                    wire:click="sort('created_at')">
                    {{ __('Date') }}
                </flux:table.column>

                {{-- Method --}}
                <flux:table.column>
                    {{ __('Method') }}
                </flux:table.column>

                {{-- Transaction Code --}}
                <flux:table.column sortable :sorted="$sortBy === 'trx' ? $sortDirection : null"
                    wire:click="sort('trx')">
                    {{ __('Transaction Code') }}
                </flux:table.column>

                {{-- Amount --}}
                <flux:table.column sortable :sorted="$sortBy === 'amount' ? $sortDirection : null"
                    wire:click="sort('amount')">
                    {{ __('Requested') }}
                </flux:table.column>

                {{-- Charge --}}
                <flux:table.column>
                    {{ __('Charge') }}
                </flux:table.column>

                {{-- To Receive --}}
                <flux:table.column>
                    {{ __('To Receive') }}
                </flux:table.column>

                {{-- Status --}}
                <flux:table.column sortable :sorted="$sortBy === 'status' ? $sortDirection : null"
                    wire:click="sort('status')">
                    {{ __('Status') }}
                </flux:table.column>

                {{-- Actions --}}
                <flux:table.column>
                    {{ __('Actions') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($withdrawals as $withdrawal)
                    <flux:table.row :key="$withdrawal->id">
                        {{-- Date --}}
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $withdrawal->created_at->format('M d, Y') }}
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $withdrawal->created_at->format('h:i A') }}
                                </span>
                            </div>
                        </flux:table.cell>

                        {{-- Method --}}
                        <flux:table.cell>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $withdrawal->withdrawMethod->name }}
                            </span>
                        </flux:table.cell>

                        {{-- Transaction Code --}}
                        <flux:table.cell>
                            <code
                                class="rounded bg-gray-100 px-2 py-1 text-xs font-mono text-gray-800 dark:bg-zinc-800 dark:text-gray-200">
                                                    {{ $withdrawal->trx }}
                                                </code>
                        </flux:table.cell>

                        {{-- Amount --}}
                        <flux:table.cell>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($withdrawal->amount, 2) }}
                            </span>
                        </flux:table.cell>

                        {{-- Charge --}}
                        <flux:table.cell>
                            <span class="text-red-600 dark:text-red-400">
                                -${{ number_format($withdrawal->charge, 2) }}
                            </span>
                        </flux:table.cell>

                        {{-- To Receive --}}
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="font-semibold text-green-600 dark:text-green-400">
                                    ${{ number_format($withdrawal->after_charge, 2) }}
                                </span>
                                @if($withdrawal->currency && $withdrawal->currency !== 'USD')
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($withdrawal->final_amount, 2) }} {{ $withdrawal->currency }}
                                    </span>
                                @endif
                            </div>
                        </flux:table.cell>

                        {{-- Status --}}
                        <flux:table.cell>
                            @php
                                $statusEnum = \App\Domain\Withdrawal\WithdrawalStatusEnum::from($withdrawal->status);
                                $color = $statusEnum->getColor();
                            @endphp
                            <flux:badge :color="$color" size="sm">
                                {{ $statusEnum->getLabel() }}
                            </flux:badge>
                        </flux:table.cell>

                        {{-- Actions --}}
                        <flux:table.cell>
                            <div class="flex items-center space-x-2">
                                {{-- View Button --}}
                                <a href="{{ route('withdrawals.view', $withdrawal->id) }}" wire:navigate
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                    title="{{ __('View') }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <div class="flex flex-col items-center justify-center py-12">
                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                                    @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $methodFilter > 0)
                                        {{ __('No withdrawals found matching your filters') }}
                                    @else
                                        {{ __('No withdrawals found') }}
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $methodFilter > 0)
                                        {{ __('Clear filters to see all withdrawals') }}
                                    @elseif($hasActiveMethods)
                                        {{ __('Get started by creating your first withdrawal') }}
                                    @else
                                        {{ __('No withdrawal methods available at the moment') }}
                                    @endif
                                </p>
                                @if($statusFilter !== 'all' || $search || $dateFrom || $dateTo || $methodFilter > 0)
                                    <button wire:click="clearFilters"
                                        class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2 text-white font-medium transition-colors hover:bg-gray-700">
                                        {{ __('Clear Filters') }}
                                    </button>
                                @elseif($hasActiveMethods)
                                    <a href="{{ route('withdrawals.create') }}" wire:navigate
                                        class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-white font-medium transition-colors hover:bg-green-700">
                                        {{ __('New Withdrawal') }}
                                    </a>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>