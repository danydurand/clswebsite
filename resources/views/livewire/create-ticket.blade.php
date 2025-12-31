<div>

    <flux:modal name="create-ticket" class="md:w-1000">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Create Ticket</flux:heading>
                <flux:text class="mt-2" color="blue">Do your first bet over a Lottery/Raffles then cand continue adding
                    bets.
                </flux:text>
            </div>

            <div class="flex w-full gap-2">
                <div class="w-1/2">
                    <flux:select wire:model.live="lottery_id" label="Lottery" placeholder="Select a Lottery">
                        @foreach ($lotteries as $lottery)
                            <flux:select.option wire:key="lott-{{ $lottery->id }}" value="{{ $lottery->id }}">
                                {{ $lottery->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="w-1/2">
                    <flux:select wire:model="raffle_id" label="Raffle" placeholder="Select a Raffle">
                        @foreach ($raffles as $raffle)
                            <flux:select.option wire:key="raf-{{ $raffle->id }}" value="{{ $raffle->id }}">
                                {{ $raffle->raffle_time }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <div class="flex w-full gap-2">
                <div class="w-1/3">
                    <flux:select wire:model="game_id" label="Game" placeholder="Select a Game">
                        @foreach ($games as $game)
                            <flux:select.option wire:key="game-{{ $game->id }}" value="{{ $game->id }}">
                                {{ $game->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="w-1/3">
                    <flux:input wire:model="sequence" label="Sequence" placeholder="Your bet sequence" />
                </div>
                <div class="w-1/3">
                    <flux:input wire:model="stake_amount" label="Stake Amount" placeholder="Stake amount" />
                </div>
            </div>

            <div class="flex w-full justify-between">
                <div class="rounded-md mb-2 flex justify-left h-[32px] items-center w-6/7">
                    <span class="font-semibold text-md text-{{$colorMessage}}-500">{{$userMessage}}</span>
                </div>

                <flux:button type="submit" variant="primary" wire:click="save" class="w-1/7">
                    Save
                </flux:button>
            </div>

    </flux:modal>
</div>