<div class="w-full">
    <!-- Gradient Header -->
    <div class="relative mb-8 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 p-8 shadow-lg">
        <div class="relative z-10">
            <flux:heading size="xl" level="1" class="text-white">{{ __('Edit Ticket') }}</flux:heading>
            <flux:subheading size="lg" class="text-blue-100">{{ __('Edit your ticket details') }}</flux:subheading>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-blue-500 opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-purple-500 opacity-20 blur-3xl"></div>
    </div>

    @include('components.flash-message')

    <!-- Action Buttons -->
    <div class="mb-6 flex flex-wrap gap-3">
        <button wire:click="back" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:from-blue-700 hover:to-purple-700 hover:shadow-lg">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
            {{ __('Back to Tickets') }}
        </button>
        
        <button wire:click="viewTicket({{ $ticket->id }})" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:bg-indigo-700 hover:shadow-lg">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
            </svg>
            {{ __('View') }}
        </button>
        
        <button wire:click="howToPlay({{ $ticket->id }})" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:bg-emerald-700 hover:shadow-lg">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
            </svg>
            {{ __('How to Play?') }}
        </button>

        <div class="ml-auto">
            <button wire:click="deleteTicket({{ $ticket->id }})" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-red-500 to-pink-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:from-red-600 hover:to-pink-700 hover:shadow-lg">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
                {{ __('Delete Ticket') }}
            </button>
        </div>
    </div>

    {{-- Ticket Details Section --}}
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
                    <flux:input readonly wire:model="ticket_stake_amount" class:input="text-right font-semibold" label="{{ __('Stake Amount') }}" />
                </div>
                <div>
                    <flux:input readonly wire:model="ticket_status" class:input="text-blue-600 font-semibold" label="{{ __('Ticket Status') }}" />
                </div>
                <div>
                    <flux:input readonly wire:model="ticket_won" label="{{ __('Won') }}" />
                </div>
                <div>
                    <flux:input readonly wire:model="ticket_prize" class:input="text-right font-semibold text-green-600" label="{{ __('Prize') }}" />
                </div>
            </div>
        </div>

        {{-- Add Bet Section --}}
        <div class="rounded-xl bg-gradient-to-r from-blue-50 to-purple-50 p-6 dark:from-blue-950/20 dark:to-purple-950/20 border border-blue-200 dark:border-blue-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Add New Bet') }}</h3>
            
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[150px]">
                    <flux:select wire:model="lottery_id" label="{{ __('Lottery') }}" placeholder="{{ __('Select a Lottery') }}">
                        @foreach ($lotteries as $lottery)
                            <flux:select.option wire:key="lott-{{ $lottery->id }}" value="{{ $lottery->id }}">
                                {{ $lottery->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <flux:select wire:model="raffle_id" label="{{ __('Raffle') }}" placeholder="{{ __('Select a Raffle') }}">
                        @foreach ($raffles as $raffle)
                            <flux:select.option wire:key="raf-{{ $raffle->id }}" value="{{ $raffle->id }}">
                                {{ $raffle->raffle_time }} ({{ $raffle->id }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <flux:select wire:model="game_id" label="{{ __('Game') }}" placeholder="{{ __('Select a Game') }}">
                        @foreach ($games as $game)
                            <flux:select.option wire:key="game-{{ $game->id }}" value="{{ $game->id }}">
                                {{ $game->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <flux:input wire:model="sequence" label="{{ __('Bet Sequence') }}" placeholder="{{ __('Your bet sequence') }}" />
                </div>
                <div class="flex-1 min-w-[120px]">
                    <flux:input wire:model="stake_amount" label="{{ __('Stake Amount') }}" placeholder="{{ __('Stake amount') }}" />
                </div>
                <div class="flex items-end min-w-[120px]">
                    <button wire:click="addBet" class="w-full rounded-lg bg-gradient-to-r from-teal-500 to-cyan-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:from-teal-600 hover:to-cyan-700 hover:shadow-lg">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            {{ __('Add Bet') }}
                        </div>
                    </button>
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
                    <flux:table.column sortable :sorted="$sortBy === 'raffle_id'" :direction="$sortDirection" align="center"
                        wire:click="sort('raffle_id')">
                        {{ __('Raffle') }}
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'game_id'" :direction="$sortDirection" align="center"
                        wire:click="sort('game_id')">
                        {{ __('Game') }}
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'sequence'" :direction="$sortDirection" align="center"
                        wire:click="sort('sequence')">
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
                    <flux:table.column align="center">
                        {{ __('Actions') }}
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($ticketDetails as $ticketDetail)
                        <flux:table.row :key="$ticketDetail->id">
                            <flux:table.cell class="text-left">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-bold">
                                        {{ substr($ticketDetail->raffle->lottery->name, 0, 2) }}
                                    </div>
                                    <span class="font-medium">{{ $ticketDetail->raffle->lottery->name }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-center font-medium">{{ $ticketDetail->raffle->raffle_time }}</flux:table.cell>
                            <flux:table.cell class="text-center">{{ $ticketDetail->game->name }}</flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $ticketDetail->sequence }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span class="font-semibold">${{ number_format($ticketDetail->stake_amount, 2) }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <flux:badge size="sm" :color="$ticketDetail->won ? 'green' : 'red'" inset="top bottom">
                                    {{ $ticketDetail->won ? __('Won') : '--' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <div class="flex gap-2 justify-center">
                                    <button 
                                        wire:click="editBet({{ $ticketDetail->id }})"
                                        class="inline-flex items-center justify-center rounded-lg bg-orange-500 p-2 text-white transition-all hover:bg-orange-600 hover:shadow-md"
                                        title="{{ __('Edit') }}">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="deleteBet({{ $ticketDetail->id }})"
                                        class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-red-500 to-pink-600 p-2 text-white transition-all hover:from-red-600 hover:to-pink-700 hover:shadow-md"
                                        title="{{ __('Delete') }}">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                        </svg>
                                    </button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>