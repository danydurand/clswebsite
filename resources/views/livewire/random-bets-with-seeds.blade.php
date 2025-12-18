<div>
    <flux:modal name="random-bets-with-seeds" class="md:w-900">
        <form class="space-y-6">
            <div>
                <flux:heading size="lg">Random Bets with my own numbers</flux:heading>
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

            <flux:input wire:model="seeds" label="Numbers" placeholder="Your numbers" />

            <flux:input wire:model="qty" label="Quantity" placeholder="Qty of random bets" />
            <flux:input wire:model="stake_amount" label="Stake Amount" placeholder="Stake amount" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary" wire:click="save">Generate</flux:button>
            </div>
        </form>
    </flux:modal>
</div>