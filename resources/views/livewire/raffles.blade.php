<div class="relative mb-6 w-5/7 mx-auto">
    <flux:heading size="xl" level="1">{{ __('Raffles') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Available raffles where you can bet') }}</flux:subheading>
    <flux:separator variant="subtle" />

    {{-- The Session Messages --}}
    @session('success')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-green-200 text-green-800 text-sm p-4 rounded shadow-lg" role="alert">
            <p>{{ $value }}</p>
        </div>
    @endsession()

    @session('error')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-red-200 text-red-800 text-sm p-4 rounded shadow-lg" role="alert">
            <p>{{ $value }}</p>
        </div>
    @endsession()

    {{-- The Buttons to create a ticket --}}
    <div class="flex justify-between w-full gap-2 mb-4 mt-4">
        <flux:modal.trigger name="create-ticket">
            <flux:button class="w-1/3 h-10" variant="primary" color="blue">Buy a Lottery Ticket</flux:button>
        </flux:modal.trigger>
        <flux:modal.trigger name="random-bets">
            <flux:button class="w-1/3 h-10" variant="primary" color="green">I want random bets</flux:button>
        </flux:modal.trigger>
        <flux:modal.trigger name="random-bets-with-seeds">
            <flux:button class="w-1/3 h-10" variant="primary" color="orange">I want random bets with my own numbers
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- The Raffles Table --}}
    <flux:table :paginate="$raffles">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'lottery_id'" :direction="$sortDirection"
                wire:click="sort('lottery_id')">
                Lottery
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'raffle_date'" :direction="$sortDirection" align="center"
                wire:click="sort('raffle_date')">
                Date
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'raffle_time'" :direction="$sortDirection" align="center"
                wire:click="sort('raffle_time')">
                Time
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'stop_sale_time'" :direction="$sortDirection"
                align="center" wire:click="sort('stop_sale_time')">
                Stop Sale Time
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" align="center"
                wire:click="sort('status')">
                Status
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($raffles as $raffle)
                <flux:table.row :key="$raffle->id">
                    <flux:table.cell>{{ $raffle->lottery->name }}</flux:table.cell>

                    <flux:table.cell class="text-center">{{ $raffle->raffle_date->format('Y-m-d') }}</flux:table.cell>

                    <flux:table.cell class="text-center">{{ $raffle->raffle_time }}</flux:table.cell>

                    <flux:table.cell class="text-center">{{ $raffle->stop_sale_time }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" :color="$raffle?->status?->getColor()" inset="top bottom">
                            {{ $raffle?->status?->getLabel() }}
                        </flux:badge>
                    </flux:table.cell>

                    {{-- <flux:table.cell>
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                    </flux:table.cell> --}}
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>



    <livewire:create-ticket />
    <livewire:random-bets />
    <livewire:random-bets-with-seeds />
    {{-- <livewire:edit-note /> --}}

</div>