<div>
    <flux:modal name="create-sport-bet" class="md:w-900">
        <form class="space-y-6">
            <div>
                <flux:heading size="lg">Create an Sport Bet</flux:heading>
                <flux:text class="mt-2">Do your bet over a Sport Event.</flux:text>
            </div>

            <flux:select wire:model="events" label="Event" placeholder="Select an Event">
                @foreach ($events as $event)
                    <flux:select.option>{{ $event->slug }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="questions" label="Question" placeholder="Select a Question">
                @foreach ($questions as $question)
                    <flux:select.option>{{ $question->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="stake_amount" label="Stake Amount" placeholder="Stake amount" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary" wire:click="save">Buy</flux:button>
            </div>
        </form>
    </flux:modal>
</div>