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

    {{-- <flux:modal.trigger name="create-ticket">
        <flux:button class="mt-4 w-5/7 bg-blue-500 text-white">Create Ticket</flux:button>
    </flux:modal.trigger> --}}

    <table class="table-auto w-5/7 bg-slate-100 shadow-md rounded-lg mt-5">
        <thead class="bg-slate-200">
            <tr class="rounded-lg">
                <th class="px-4 py-2 text-center">Created At</th>
                <th class="px-4 py-2 text-center">Status</th>
                <th class="px-4 py-2 text-center">Code</th>
                <th class="px-4 py-2 text-right">Stake Amount</th>
                <th class="px-4 py-2 text-center">Won?</th>
                <th class="px-4 py-2 text-right">Prize</th>
                <th class="px-4 py-2 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $ticket)
                <tr class="border-t">
                    <td class="px-4 py-2 text-center">{{ $ticket->created_at }}</td>
                    <td class="px-4 py-2 text-center">{{ $ticket->status }}</td>
                    <td class="px-4 py-2 text-center">{{ $ticket->code }}</td>
                    <td class="px-4 py-2 text-right">{{ $ticket->stake_amount }}</td>
                    <td class="px-4 py-2 text-center">{{ $ticket->won }}</td>
                    <td class="px-4 py-2 text-right">{{ $ticket->prize }}</td>
                    <td class="px-4 py-2 text-center">
                        <flux:button class="bg-blue-500 text-white" wire:click="edit({{ $ticket->id }})">
                            Edit
                        </flux:button>
                        <flux:button variant="danger" wire:click="delete({{ $ticket->id }})">
                            Delete
                        </flux:button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-2 text-center">No tickets found</td>
                    @php
                        $ticket = null;
                    @endphp
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="w-5/7 mt-4">
        {{ $tickets->links() }}
    </div>

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

    {{-- <livewire:create-ticket />
    <livewire:edit-ticket /> --}}

</div>