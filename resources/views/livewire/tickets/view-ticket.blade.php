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
        <x-ui.loading-button 
            action="back" 
            color="blue" 
            gradient="true"
            :icon="'<path d=\'M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z\' />'">
            {{ __('Back to Tickets') }}
        </x-ui.loading-button>

        <x-ui.loading-button 
            action="editTicket({{ $ticket->id }})" 
            color="orange"
            :icon="'<path d=\'M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z\' />'">
            {{ __('Edit') }}&nbsp;&nbsp;{{ __('(Add/Remove Bets)') }}
        </x-ui.loading-button>

        <x-ui.loading-button 
            action="printTicket" 
            color="green" 
            gradient="true"
            :icon="'<path d=\'M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z\' />'">
            {{ __('Print Ticket') }}
        </x-ui.loading-button>

        <x-ui.loading-button 
            action="sendEmail" 
            color="blue"
            loading-text="{{ __('Sending...') }}"
            :icon="'<path d=\'M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z\' />'">
            {{ __('Send Email') }}
        </x-ui.loading-button>

        <x-ui.loading-button 
            action="sendWhatsApp" 
            color="green"
            loading-text="{{ __('Sending...') }}"
            :icon="'<path d=\'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z\' />'">
            {{ __('Send WhatsApp') }}
        </x-ui.loading-button>

        <div class="ml-auto">
            <x-ui.loading-button 
                action="$set('showDeleteConfirm', true)" 
                color="red" 
                gradient="true"
                :icon="'<path d=\'M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z\' />'">
                {{ __('Delete Ticket') }}
            </x-ui.loading-button>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <x-ui.confirm-dialog
        wire:model="showDeleteConfirm"
        type="danger"
        title="{{ __('Delete Ticket?') }}"
        message="{{ __('Are you sure you want to delete this ticket? This action cannot be undone and all associated bets will be permanently removed.') }}"
        confirm-text="{{ __('Yes, Delete') }}"
        cancel-text="{{ __('Cancel') }}"
        confirm-action="deleteTicket({{ $ticket->id }})"
    />

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