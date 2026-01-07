<div class="w-full">
    <!-- Gradient Header -->
    <div class="relative mb-8 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 p-8 shadow-lg">
        <div class="relative z-10">
            <flux:heading size="xl" level="1" class="text-white">{{ __('View Ticket') }}</flux:heading>
            <flux:subheading size="lg" class="text-blue-100">{{ __('View your ticket details') }}</flux:subheading>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-blue-500 opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-purple-500 opacity-20 blur-3xl"></div>
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

    <!-- Action Buttons -->
    <div class="mb-6 flex flex-wrap gap-3">
        <button wire:click="back"
            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:from-blue-700 hover:to-purple-700 hover:shadow-lg">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
            </svg>
            {{ __('Back to Tickets') }}
        </button>

        <button wire:click="editTicket({{ $ticket->id }})"
            class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:bg-orange-600 hover:shadow-lg">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
            </svg>
            {{ __('Edit') }}&nbsp;&nbsp;{{ __('(Add/Remove Bets)') }}
        </button>

        <div class="ml-auto">
            <button wire:click="deleteTicket({{ $ticket->id }})"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-red-500 to-pink-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:from-red-600 hover:to-pink-700 hover:shadow-lg">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                </svg>
                {{ __('Delete Ticket') }}
            </button>
        </div>
    </div>

    {{-- Ticket Data --}}
    <div class="space-y-6">
        <!-- Ticket Information Card -->
        <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Ticket Information') }}</h3>

            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <flux:input readonly wire:model="ticket_id" label="{{ __('ID') }}" />
                </div>
                <div class="md:col-span-2">
                    <flux:input readonly wire:model="ticket_code" label="{{ __('Code') }}" />
                </div>
                <div>
                    <flux:input readonly wire:model="ticket_created_at" label="{{ __('Created At') }}" />
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-4">
                <div>
                    <flux:input readonly wire:model="ticket_stake_amount" class:input="text-right font-semibold"
                        label="{{ __('Stake Amount') }}" />
                </div>
                <div>
                    <flux:input readonly wire:model="ticket_status" class:input="text-blue-600 font-semibold"
                        label="{{ __('Ticket Status') }}" />
                </div>
                <div>
                    <flux:input readonly wire:model="ticket_won" label="{{ __('Won') }}" />
                </div>
                <div>
                    <flux:input readonly wire:model="ticket_prize" class:input="text-right font-semibold text-green-600"
                        label="{{ __('Prize') }}" />
                </div>
            </div>
        </div>

        {{-- The Ticket Details Table --}}
        <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Bets on this Ticket') }}</h3>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column align="left">
                        {{ __('Lottery') }}
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'raffle_id'" :direction="$sortDirection"
                        align="center" wire:click="sort('raffle_id')">
                        {{ __('Raffle') }}
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'game_id'" :direction="$sortDirection"
                        align="center" wire:click="sort('game_id')">
                        {{ __('Game') }}
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'sequence'" :direction="$sortDirection"
                        align="center" wire:click="sort('sequence')">
                        {{ __('Bet Sequence') }}
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'stake_amount'" :direction="$sortDirection"
                        align="center" wire:click="sort('stake_amount')">
                        {{ __('Stake Amount') }}
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'won'" :direction="$sortDirection" align="center"
                        wire:click="sort('won')">
                        {{ __('Won') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($ticketDetails as $ticketDetail)
                        <flux:table.row :key="$ticketDetail->id">
                            <flux:table.cell class="text-left">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-bold">
                                        {{ substr($ticketDetail->raffle->lottery->name, 0, 2) }}
                                    </div>
                                    <span class="font-medium">{{ $ticketDetail->raffle->lottery->name }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-center font-medium">{{ $ticketDetail->raffle->raffle_time }}
                            </flux:table.cell>
                            <flux:table.cell class="text-center">{{ $ticketDetail->game->name }}</flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span
                                    class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $ticketDetail->sequence }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span class="font-semibold">${{ number_format($ticketDetail->stake_amount, 2) }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <flux:badge size="sm" :color="$ticketDetail->won ? 'green' : 'red'" inset="top bottom">
                                    {{ $ticketDetail->won ? __('Won') : '--' }}
                                </flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>