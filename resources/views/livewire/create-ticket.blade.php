<div>
    <flux:modal name="create-ticket" class="md:w-[800px]">
        <form class="space-y-6">
            <!-- Modal Header -->
            <div class="relative overflow-hidden rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 p-6 -m-6 mb-6">
                <div class="relative z-10">
                    <flux:heading size="lg" class="text-white">{{ __('Create Ticket') }}</flux:heading>
                    <flux:text class="mt-2 text-blue-100">
                        {{ __('Do your first bet over a Lottery/Raffle then you can continue adding bets.') }}
                    </flux:text>
                </div>
                <!-- Decorative Elements -->
                <div class="absolute top-0 right-0 h-32 w-32 rounded-full bg-blue-500 opacity-20 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-purple-500 opacity-20 blur-2xl"></div>
            </div>

            <!-- Lottery and Raffle Selection -->
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-zinc-800/50">
                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ __('Select Lottery & Raffle') }}
                </h4>
                <div class="flex w-full gap-4">
                    <div class="w-1/2">
                        <flux:select wire:model.live="lottery_id" label="{{ __('Lottery') }}"
                            placeholder="{{ __('Select a Lottery') }}">
                            @foreach ($lotteries as $lottery)
                                <flux:select.option wire:key="lott-{{ $lottery->id }}" value="{{ $lottery->id }}">
                                    {{ $lottery->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="w-1/2">
                        <flux:select wire:model="raffle_id" label="{{ __('Raffle') }}"
                            placeholder="{{ __('Select a Raffle') }}">
                            @foreach ($raffles as $raffle)
                                <flux:select.option wire:key="raf-{{ $raffle->id }}" value="{{ $raffle->id }}">
                                    {{ $raffle->raffle_time }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </div>

            <!-- Bet Details -->
            <div
                class="rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 p-4 dark:from-blue-950/20 dark:to-purple-950/20">
                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Bet Details') }}</h4>
                <div class="flex w-full gap-4">
                    <div class="w-1/3">
                        <flux:select wire:model="game_id" label="{{ __('Game') }}"
                            placeholder="{{ __('Select a Game') }}">
                            @foreach ($games as $game)
                                <flux:select.option wire:key="game-{{ $game->id }}" value="{{ $game->id }}">
                                    {{ $game->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="w-1/3">
                        <flux:input wire:model="sequence" label="{{ __('Sequence') }}"
                            placeholder="{{ __('Your bet sequence') }}" />
                    </div>
                    <div class="w-1/3">
                        <flux:input wire:model="stake_amount" label="{{ __('Stake Amount') }}"
                            placeholder="{{ __('Stake amount') }}" />
                    </div>
                </div>
            </div>

            <!-- Message and Action -->
            <div class="flex items-center justify-between gap-4 border-t border-gray-200 pt-4 dark:border-zinc-700">
                <div class="flex-1">
                    @if($userMessage)
                        <div
                            class="rounded-lg px-3 py-2 {{ $colorMessage === 'green' ? 'bg-green-50 dark:bg-green-950/20' : 'bg-red-50 dark:bg-red-950/20' }}">
                            <span
                                class="text-sm font-semibold {{ $colorMessage === 'green' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                {{ $userMessage }}
                            </span>
                        </div>
                    @endif
                </div>

                <button type="submit" wire:click="save"
                    class="rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-2.5 font-semibold text-white shadow-md transition-all hover:from-blue-700 hover:to-purple-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </flux:modal>
</div>