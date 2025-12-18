<div>
    <flux:modal name="create-ticket" class="md:w-900">
        <form class="space-y-6">
            <div>
                <flux:heading size="lg">Create Ticket</flux:heading>
                <flux:text class="mt-2">Do your bet over a Lottery.</flux:text>
            </div>

            <div class="flex justify-between w-full gap-2">
                <flux:select wire:model="lotteries" label="Lottery" placeholder="Select a Lottery">
                    @foreach ($lotteries as $lottery)
                        <flux:select.option>{{ $lottery->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="games" label="Game" placeholder="Select a Game">
                    @foreach ($games as $game)
                        <flux:select.option>{{ $game->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:input wire:model="sequence" label="Sequence" placeholder="Your bet sequence" />
            <flux:input wire:model="stake_amount" label="Stake Amount" placeholder="Stake amount" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary" wire:click="save">Buy</flux:button>
            </div>
        </form>
    </flux:modal>
</div>