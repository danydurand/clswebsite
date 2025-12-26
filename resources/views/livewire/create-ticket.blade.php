<div>

    <flux:modal name="create-ticket" class="md:w-1000">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Create Ticket</flux:heading>
                <flux:text class="mt-2">Do your bet over a Lottery.</flux:text>
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
                <div class="rounded-md mb-2 flex justify-center h-[32px] items-center w-6/7">
                    <span class="font-semibold text-md text-{{$colorMessage}}-700">{{$userMessage}}</span>
                </div>

                <flux:button type="submit" variant="primary" wire:click="save" class="w-1/7">Add Bet
                </flux:button>
            </div>

            {{-- The Ticket Details Table --}}
            <flux:table :paginate="$ticketDetails">
                <flux:table.columns>
                    <flux:table.column>
                        Lottery
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'raffle_id'" :direction="$sortDirection"
                        wire:click="sort('raffle_id')">
                        Raffle
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'game_id'" :direction="$sortDirection"
                        align="center" wire:click="sort('game_id')">
                        Game
                    </flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'sequence'" :direction="$sortDirection"
                        align="center" wire:click="sort('sequence')">
                        Sequence
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
                            <flux:table.cell>{{ $ticketDetail->raffle->lottery->name }}</flux:table.cell>
                            <flux:table.cell>{{ $ticketDetail->raffle->raffle_time }}</flux:table.cell>
                            <flux:table.cell>{{ $ticketDetail->game->name }}</flux:table.cell>
                            <flux:table.cell class="text-center">{{ $ticketDetail->sequence }}</flux:table.cell>

                            <flux:table.cell class="text-center">{{ $ticketDetail->stake_amount }}</flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" :color="$ticketDetail->won ? 'green' : 'red'" inset="top bottom">
                                    {{ $ticketDetail->won ? 'Won' : 'Lost' }}
                                </flux:badge>
                            </flux:table.cell>

                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

    </flux:modal>
</div>