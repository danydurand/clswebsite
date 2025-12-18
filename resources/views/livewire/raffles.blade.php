<div class="relative mb-6 w-5/7 mx-auto">
    <flux:heading size="xl" level="1">{{ __('Raffles') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Available raffles where you can bet') }}</flux:subheading>
    <flux:separator variant="subtle" />

    @session('success')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-green-600 text-white text-sm p-4 rounded shadow-lg" role="alert">
            <p>{{ $value }}</p>
        </div>
    @endsession()

    <div class="flex justify-between w-5/7 gap-2">
        <flux:modal.trigger name="create-ticket">
            <flux:button class="mt-4 w-1/3" variant="primary" color="blue">Buy a Lottery Ticket</flux:button>
        </flux:modal.trigger>
        <flux:modal.trigger name="random-bets">
            <flux:button class="mt-4 w-1/3" variant="primary" color="green">Random Bets</flux:button>
        </flux:modal.trigger>
        <flux:modal.trigger name="random-bets-with-seeds">
            <flux:button class="mt-4 w-1/3" variant="primary" color="orange">Random Bets with my own numbers
            </flux:button>
        </flux:modal.trigger>
    </div>
    <table class="table-auto w-5/7 bg-slate-100 shadow-md rounded-lg mt-5">
        <thead class="bg-slate-200">
            <tr class="rounded-lg">
                <th class="px-4 py-2">Lottery</th>
                <th class="px-4 py-2 text-center">Code</th>
                <th class="px-4 py-2 text-center">Date</th>
                <th class="px-4 py-2 text-center">Time</th>
                <th class="px-4 py-2 text-center">Stop Sale Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($raffles as $raffle)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $raffle->lottery->name }}</td>
                    <td class="px-4 py-2 text-center">{{ $raffle->code }}</td>
                    <td class="px-4 py-2 text-center">{{ $raffle->raffle_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 text-center">{{ $raffle->raffle_time }}</td>
                    <td class="px-4 py-2 text-center">{{ $raffle->stop_sale_time }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="w-5/7 mt-4">
        {{ $raffles->links() }}
    </div>


    <livewire:create-ticket />
    <livewire:random-bets />
    <livewire:random-bets-with-seeds />
    {{-- <livewire:edit-note /> --}}

</div>