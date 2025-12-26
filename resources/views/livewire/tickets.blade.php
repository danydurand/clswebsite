<div class="relative mb-6 w-6/7 mx-auto">
    <flux:heading size="xl" level="1">{{ __('My Tickets') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('My lottery tickets') }}</flux:subheading>
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

    {{-- <flux:modal.trigger name="create-ticket">
        <flux:button class="mt-4 w-5/7 bg-blue-500 text-white">Create Ticket</flux:button>
    </flux:modal.trigger> --}}

    {{-- The Tickets Table --}}
    <flux:table :paginate="$tickets">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" align="center"
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
            <flux:table.column sortable :sorted="$sortBy === 'stake_amount'" :direction="$sortDirection" align="center"
                wire:click="sort('stake_amount')">
                Stake Amount
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'won'" :direction="$sortDirection" align="center"
                wire:click="sort('won')">
                Won?
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'prize'" :direction="$sortDirection" align="center"
                wire:click="sort('prize')">
                Prize
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" align="center"
                wire:click="sort('status')">
                Status
            </flux:table.column>
            <flux:table.column align="center">
                Actions
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($tickets as $ticket)
                <flux:table.row :key="$ticket->id">
                    <flux:table.cell align="center">{{ $ticket->id }}</flux:table.cell>

                    <flux:table.cell align="center">{{ $ticket->created_at }}</flux:table.cell>

                    <flux:table.cell align="center">{{ $ticket->code }}</flux:table.cell>

                    <flux:table.cell align="center">{{ $ticket->stake_amount }}</flux:table.cell>

                    <flux:table.cell align="center">{{ $ticket->won }}</flux:table.cell>

                    <flux:table.cell align="center">{{ $ticket->prize }}</flux:table.cell>

                    <flux:table.cell align="center">
                        <flux:badge size="sm" :color="$ticket->status->getColor()" inset="top bottom">
                            {{ $ticket->status->getLabel() }}
                        </flux:badge>
                    </flux:table.cell>


                    <flux:table.cell align="center">
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />

                            <flux:menu>
                                <flux:modal.trigger name="view-ticket">
                                    <flux:menu.item icon="eye" wire:click="viewTicket({{ $ticket->id }})">View
                                    </flux:menu.item>
                                </flux:modal.trigger>
                                <flux:menu.item icon="trash" variant="danger" wire:click="deleteTicket({{ $ticket->id }})">
                                    Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>



    {{-- <div class="w-5/7 mt-4">
        {{ $tickets->links() }}
    </div> --}}

    <flux:modal name="delete-ticket" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Ticket?</flux:heading>

                <flux:text class="mt-2 text-center">
                    Are you sure you want to delete the ticket with the code:
                    <br><br>
                    <strong>{{ $ticket?->code }}</strong>
                    <br><br>
                    This action cannot be reversed.
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger" wire:click="deleteTicket()">Delete Ticket
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- <livewire:create-ticket /> --}}

    {{-- View Ticket Modal --}}
    <flux:modal name="view-ticket" class="md:w-1200">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">View Ticket</flux:heading>
                <flux:text class="mt-2">View your ticket details.</flux:text>
            </div>

            <div class="flex w-full gap-2">
                <div class="w-1/6">
                    <flux:input disabled wire:model="ticket_id" label="ID" />
                </div>
                <div class="w-5/6">
                    <flux:input disabled wire:model="ticket_code" label="Code" />
                </div>
            </div>

            <div class="flex w-full gap-2">
                <div class="w-1/2">
                    <flux:input disabled wire:model="ticket_created_at" label="Created At" />
                </div>
                <div class="w-1/2 flex justify-center items-center mt-6">
                    <flux:badge size="lg" :color="$ticket_status_color">
                        {{ $ticket_status }}
                    </flux:badge>
                </div>
            </div>

            <div class="flex w-full gap-2">
                <flux:input disabled wire:model="ticket_stake_amount" label="Stake Amount" align="right" />
                <flux:input disabled wire:model="ticket_won" label="Won?" align="center" />
                <flux:input disabled wire:model="ticket_prize" label="Prize" align="right" />
            </div>

            <div class="flex w-full justify-between">
                <div class="rounded-md mb-2 flex justify-center h-[32px] items-center">
                    <span class="font-semibold text-md text-{{$colorMessage}}-700">{{$userMessage}}</span>
                </div>

            </div>
        </div>
    </flux:modal>

    {{-- <livewire:edit-ticket /> --}}

</div>