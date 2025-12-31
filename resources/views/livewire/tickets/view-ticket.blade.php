<div class="relative mb-6 w-6/7 mx-auto">
    <flux:heading size="xl" level="1">{{ __('View Ticket') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('View your ticket details') }}</flux:subheading>
    <flux:separator variant="subtle" />

    @session('success')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-green-600 text-white text-sm p-4 rounded shadow-lg" role="alert">
            <p>{{ $value }}</p>
        </div>
    @endsession()

    @session('error')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-red-600 text-white text-sm p-4 rounded shadow-lg" role="alert">
            <p>{{ $value }}</p>
        </div>
    @endsession()

    <div class="flex w-full max-w-5xl justify-start gap-2 mt-4 mb-4">
        <flux:button wire:click="back" icon="arrow-left" variant="primary" color="blue">Back to Tickets</flux:button>
        <flux:button wire:click="editTicket({{ $ticket->id }})" icon="pencil-square" variant="primary" color="orange">
            Edit&nbsp;&nbsp;(Add/Remove Bets)
        </flux:button>
        <div class="flex justify-end w-full">
            <flux:button wire:click="deleteTicket({{ $ticket->id }})" icon="trash" variant="primary" color="rose">
                Delete Ticket
            </flux:button>
        </div>
    </div>

    {{-- Ticket Data --}}
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
                <flux:input readonly wire:model="ticket_status" label="Ticket Status" />
            </div>
        </div>

        <div class="flex gap-2 mb-12">
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_stake_amount" label="Stake Amount" />
            </div>
            <div class="w-3/6">
                <flux:input readonly wire:model="ticket_won" label="Won" />
            </div>
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_prize" label="Prize" />
            </div>
            <div class="w-1/6">
                <flux:input readonly wire:model="ticket_payment_status" label="Payment Status" />
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

                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>

</div>