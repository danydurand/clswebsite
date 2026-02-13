<div class="w-full">
    {{-- Flash Messages --}}
    @include('components.flash-message')

    {{-- Header --}}
    <x-ui.list-header :title="__('My Tickets')" :buttonText="__('Buy a Lottery Ticket')" buttonRoute="#"
        :showButton="false" />

    {{-- Create Ticket Button --}}
    <div class="mb-6">
        <flux:modal.trigger name="create-ticket">
            <button
                class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-4 text-left shadow-lg transition-all hover:shadow-xl hover:scale-105">
                <div class="relative z-10 flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ __('Buy a Lottery Ticket') }}</h3>
                        <p class="text-sm text-blue-100">{{ __('Create a new ticket and place your bets') }}</p>
                    </div>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-700 opacity-0 transition-opacity group-hover:opacity-100">
                </div>
            </button>
        </flux:modal.trigger>
    </div>

    {{-- Tickets Table --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <flux:table :paginate="$tickets">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" align="start"
                    wire:click="sort('id')">
                    {{ __('ID') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                    align="center" wire:click="sort('created_at')">
                    {{ __('Created At') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'code'" :direction="$sortDirection" align="center"
                    wire:click="sort('code')">
                    {{ __('Code') }}
                </flux:table.column>
                <flux:table.column align="center">
                    {{ __('Qty Bets') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'stake_amount'" :direction="$sortDirection" align="end"
                    wire:click="sort('stake_amount')">
                    {{ __('Stake Amount') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" align="center"
                    wire:click="sort('status')">
                    {{ __('Status') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'won'" :direction="$sortDirection" align="center"
                    wire:click="sort('won')">
                    {{ __('Won?') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'prize'" :direction="$sortDirection" align="center"
                    wire:click="sort('prize')">
                    {{ __('Prize') }}
                </flux:table.column>
                <flux:table.column align="center">
                    {{ __('Actions') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($tickets as $ticket)
                    <flux:table.row :key="$ticket->id">
                        <flux:table.cell align="start">
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-bold">
                                    #{{ $ticket->id }}
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="center" class="text-sm">
                            {{ $ticket->created_at->format('Y-m-d H:i') }}
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <span
                                class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $ticket->code }}</span>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <span
                                class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-blue-100 to-purple-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:from-blue-950/20 dark:to-purple-950/20 dark:text-blue-400">
                                {{ $ticket->ticketDetails->count() }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <span
                                class="font-semibold text-gray-900 dark:text-white">${{ number_format($ticket->stake_amount, 2) }}</span>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <flux:badge size="md" :color="$ticket->status?->getColor()" inset="top bottom">
                                {{ $ticket->status?->getLabel() }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            @if($ticket->won)
                                <div
                                    class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 px-3 py-1 text-sm font-semibold text-green-700 dark:from-green-950/20 dark:to-emerald-950/20 dark:text-green-400">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                    </svg>
                                    {{ __('Yes') }}
                                </div>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('No') }}</span>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            @if($ticket->prize)
                                <span
                                    class="font-bold text-green-600 dark:text-green-400">${{ number_format($ticket->prize, 2) }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <div class="flex gap-2 justify-center">
                                <button wire:click="viewTicket({{ $ticket->id }})"
                                    class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-teal-500 to-cyan-600 p-2 text-white transition-all hover:from-teal-600 hover:to-cyan-700 hover:shadow-md"
                                    title="View">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                    </svg>
                                </button>
                                <button wire:click="editTicket({{ $ticket->id }})"
                                    class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-amber-600 p-2 text-white transition-all hover:from-orange-600 hover:to-amber-700 hover:shadow-md"
                                    title="Edit">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                    </svg>
                                </button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <livewire:create-ticket />
</div>