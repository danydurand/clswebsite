<div class="relative mb-6 w-6/7 mx-auto">
    <flux:heading size="xl" level="1">{{ __('Edit Ticket') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Edit your ticket details') }}</flux:subheading>
    <flux:separator variant="subtle" />

    @include('components.flash-message')

    <div class="flex w-full max-w-5xl gap-2 mt-4 mb-4">

        <flux:button wire:click="back" icon="arrow-left" variant="primary" color="blue">
            Back to Tickets
        </flux:button>
        <flux:button wire:click="viewTicket({{ $ticket->id }})" icon="eye" variant="primary" color="teal">
            View
        </flux:button>
        <flux:button wire:click="howToPlay({{ $ticket->id }})" icon="information-circle" variant="primary" color="lime">
            How to Play?
        </flux:button>

        <div class="flex justify-end w-full">
            <flux:button wire:click="deleteTicket({{ $ticket->id }})" icon="trash" variant="primary" color="rose">
                Delete Ticket
            </flux:button>
        </div>

    </div>

    {{-- Ticket Details Section --}}
    <div class="space-y-6 w-full max-w-5xl self-center">

        <div class="flex gap-2">
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_id" label="ID" />
            </div>
            <div class="w-3/6">
                <flux:input readonly wire:model="ticket_code" label="Code" />
            </div>
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_created_at" label="Created At" />
            </div>
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_status" class:input="text-blue-600" label="Ticket Status" />
            </div>
        </div>

        <div class="flex gap-2">
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_stake_amount" class:input="text-right" label="Stake Amount" />
            </div>
            <div class="w-3/6">
                <flux:input readonly wire:model="ticket_won" label="Won" />
            </div>
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_prize" class:input="text-right" label="Prize" />
            </div>
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_payment_status" class:input="text-orange-600"
                    label="Payment Status" />
            </div>
        </div>

        {{-- Add Bet Section --}}
        <div class="flex w-full gap-2">
            <div class="w-1/6">
                <flux:select wire:model="lottery_id" label="Lottery" placeholder="Select a Lottery">
                    @foreach ($lotteries as $lottery)
                        <flux:select.option wire:key="lott-{{ $lottery->id }}" value="{{ $lottery->id }}">
                            {{ $lottery->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="w-1/6">
                <flux:select wire:model="raffle_id" label="Raffle" placeholder="Select a Raffle">
                    @foreach ($raffles as $raffle)
                        <flux:select.option wire:key="raf-{{ $raffle->id }}" value="{{ $raffle->id }}">
                            {{ $raffle->raffle_time }} ({{ $raffle->id }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="w-1/6">
                <flux:select wire:model="game_id" label="Game" placeholder="Select a Game">
                    @foreach ($games as $game)
                        <flux:select.option wire:key="game-{{ $game->id }}" value="{{ $game->id }}">
                            {{ $game->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="w-1/6">
                <flux:input wire:model="sequence" label="Bet Sequence" placeholder="Your bet sequence" />
            </div>
            <div class="w-1/6">
                <flux:input wire:model="stake_amount" label="Stake Amount" placeholder="Stake amount" />
            </div>
            <div class="w-1/6 mt-6">
                <flux:button wire:click="addBet" icon="plus" variant="primary" color="teal">
                    Add Bet
                </flux:button>
            </div>
        </div>

        {{-- The Ticket Details Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column align="left">
                    Lottery
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'raffle_id'" :direction="$sortDirection" align="center"
                    wire:click="sort('raffle_id')">
                    Raffle
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'game_id'" :direction="$sortDirection" align="center"
                    wire:click="sort('game_id')">
                    Game
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'sequence'" :direction="$sortDirection" align="center"
                    wire:click="sort('sequence')">
                    Bet Sequence
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'stake_amount'" :direction="$sortDirection"
                    align="center" wire:click="sort('stake_amount')">
                    Stake Amount
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'won'" :direction="$sortDirection" align="center"
                    wire:click="sort('won')">
                    Won
                </flux:table.column>
                <flux:table.column align="start">
                    Actions
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($ticketDetails as $ticketDetail)
                    <flux:table.row :key="$ticketDetail->id">
                        <flux:table.cell class="text-left">{{ $ticketDetail->raffle->lottery->name }}</flux:table.cell>
                        <flux:table.cell class="text-center">{{ $ticketDetail->raffle->raffle_time }}</flux:table.cell>
                        <flux:table.cell class="text-center">{{ $ticketDetail->game->name }}</flux:table.cell>
                        <flux:table.cell class="text-center">{{ $ticketDetail->sequence }}</flux:table.cell>
                        <flux:table.cell class="text-center">{{ $ticketDetail->stake_amount }}</flux:table.cell>
                        <flux:table.cell class="text-center">
                            <flux:badge size="sm" :color="$ticketDetail->won ? 'green' : 'red'" inset="top bottom">
                                {{ $ticketDetail->won ? 'Won' : '--' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="center" class="flex gap-2">
                            <flux:button size="sm" variant="primary" icon="pencil-square" color="orange"
                                wire:click="editBet({{ $ticketDetail->id }})">
                                {{-- Edit --}}
                            </flux:button>
                            <flux:button size="sm" variant="primary" icon="trash" color="rose"
                                wire:click="deleteBet({{ $ticketDetail->id }})">
                                {{-- Delete --}}
                            </flux:button>
                        </flux:table.cell>

                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>


</div>