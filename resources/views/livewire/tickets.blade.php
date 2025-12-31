<div class="relative mb-6 w-6/7 mx-auto">
    <flux:heading size="xl" level="1">{{ __('My Tickets') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('My lottery tickets') }}</flux:subheading>
    <flux:separator variant="subtle" />

    {{-- @include('components.flash-message') --}}


    <flux:modal.trigger name="create-ticket">
        {{-- <flux:button class="mt-4 w-5/7 bg-blue-500 text-white">Create Ticket</flux:button> --}}
        <flux:button class="w-1/3 h-10" variant="primary" color="blue">Buy a Lottery Ticket</flux:button>
    </flux:modal.trigger>

    {{-- The Tickets Table --}}
    <flux:table :paginate="$tickets">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" align="start"
                wire:click="sort('id')">
                ID
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" align="center"
                wire:click="sort('created_at')">
                Created At
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'code'" :direction="$sortDirection" align="center"
                wire:click="sort('code')">
                Code
            </flux:table.column>
            <flux:table.column>
                Qty Bets
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'stake_amount'" :direction="$sortDirection" align="end"
                wire:click="sort('stake_amount')">
                Stake Amount
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" align="center"
                wire:click="sort('status')">
                Status
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'won'" :direction="$sortDirection" align="center"
                wire:click="sort('won')">
                Won?
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'prize'" :direction="$sortDirection" align="center"
                wire:click="sort('prize')">
                Prize
            </flux:table.column>
            <flux:table.column align="start">
                Actions
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($tickets as $ticket)
                <flux:table.row :key="$ticket->id">
                    <flux:table.cell align="start" class="text-lg">{{ $ticket->id }}</flux:table.cell>

                    <flux:table.cell align="center" class="text-lg">{{ $ticket->created_at }}</flux:table.cell>

                    <flux:table.cell align="center" class="text-lg">{{ $ticket->code }}</flux:table.cell>

                    <flux:table.cell align="center" class="text-lg">{{ $ticket->ticketDetails->count() }}</flux:table.cell>

                    <flux:table.cell align="end" class="text-lg">{{ $ticket->stake_amount }}</flux:table.cell>

                    <flux:table.cell align="center">
                        <flux:badge size="md" :color="$ticket->status?->getColor()" inset="top bottom">
                            {{ $ticket->status?->getLabel() }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell align="center" class="text-lg">{{ $ticket->won }}</flux:table.cell>

                    <flux:table.cell align="center" class="text-lg">{{ $ticket->prize }}</flux:table.cell>


                    <flux:table.cell align="center" class="flex gap-2">
                        <flux:button size="sm" variant="primary" icon="eye" color="teal"
                            wire:click="viewTicket({{ $ticket->id }})">
                            {{-- View --}}
                        </flux:button>
                        <flux:button size="sm" variant="primary" icon="pencil-square" color="orange"
                            wire:click="editTicket({{ $ticket->id }})">
                            {{-- Edit --}}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>


    <livewire:create-ticket />


</div>