<div class="w-full">
    {{-- Flash Messages --}}
    @include('components.flash-message')

    {{-- Header --}}
    <x-ui.list-header :title="__('Lottery Games')" :subtitle="__('Available draws where you can bet')"
        gradientFrom="blue-600" gradientTo="purple-600" subtitleColor="blue-100" decorColor1="blue-500"
        decorColor2="purple-500" />

    {{-- Action Buttons --}}
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <flux:modal.trigger name="create-ticket">
            <button
                class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-left shadow-lg transition-all hover:shadow-xl hover:scale-105">
                <div class="relative z-10">
                    <div
                        class="mb-2 flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22 10V6c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2zm-2-1.46c-1.19.69-2 1.99-2 3.46s.81 2.77 2 3.46V18H4v-2.54c1.19-.69 2-1.99 2-3.46 0-1.48-.8-2.77-1.99-3.46L4 6h16v2.54z" />
                        </svg>
                    </div>
                    <h3 class="mb-1 text-lg font-bold text-white">{{ __('Buy Lottery Ticket') }}</h3>
                    <p class="text-sm text-blue-100">{{ __('Create your own ticket') }}</p>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-700 opacity-0 transition-opacity group-hover:opacity-100">
                </div>
            </button>
        </flux:modal.trigger>

        <flux:modal.trigger name="random-bets">
            <button
                class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-left shadow-lg transition-all hover:shadow-xl hover:scale-105">
                <div class="relative z-10">
                    <div
                        class="mb-2 flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM7.5 18c-.83 0-1.5-.67-1.5-1.5S6.67 15 7.5 15s1.5.67 1.5 1.5S8.33 18 7.5 18zm0-9C6.67 9 6 8.33 6 7.5S6.67 6 7.5 6 9 6.67 9 7.5 8.33 9 7.5 9zm4.5 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4.5 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm0-9c-.83 0-1.5-.67-1.5-1.5S15.67 6 16.5 6s1.5.67 1.5 1.5S17.33 9 16.5 9z" />
                        </svg>
                    </div>
                    <h3 class="mb-1 text-lg font-bold text-white">{{ __('Random Bets') }}</h3>
                    <p class="text-sm text-green-100">{{ __('Let us pick for you') }}</p>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-700 opacity-0 transition-opacity group-hover:opacity-100">
                </div>
            </button>
        </flux:modal.trigger>

        <flux:modal.trigger name="random-bets-with-seeds">
            <button
                class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-orange-500 to-amber-600 p-6 text-left shadow-lg transition-all hover:shadow-xl hover:scale-105">
                <div class="relative z-10">
                    <div
                        class="mb-2 flex h-12 w-12 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.5 4.5c-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-1.45-1.1-3.55-1.5-5.5-1.5zM21 18.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z" />
                        </svg>
                    </div>
                    <h3 class="mb-1 text-lg font-bold text-white">{{ __('Random with Seeds') }}</h3>
                    <p class="text-sm text-orange-100">{{ __('Your numbers + random') }}</p>
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-orange-600 to-amber-700 opacity-0 transition-opacity group-hover:opacity-100">
                </div>
            </button>
        </flux:modal.trigger>
    </div>

    {{-- Raffles Table --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <flux:table :paginate="$raffles">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortBy === 'lottery_id'" :direction="$sortDirection"
                    wire:click="sort('lottery_id')">
                    {{ __('Lottery') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'raffle_date'" :direction="$sortDirection"
                    align="center" wire:click="sort('raffle_date')">
                    {{ __('Date') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'raffle_time'" :direction="$sortDirection"
                    align="center" wire:click="sort('raffle_time')">
                    {{ __('Time') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'stop_sale_time'" :direction="$sortDirection"
                    align="center" wire:click="sort('stop_sale_time')">
                    {{ __('Stop Sale Time') }}
                </flux:table.column>
                <flux:table.column align="center">
                    {{ __('Remaining Time') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" align="center"
                    wire:click="sort('status')">
                    {{ __('Status') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($raffles as $raffle)
                    <flux:table.row :key="$raffle->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-bold">
                                    {{ substr($raffle->lottery->name, 0, 2) }}
                                </div>
                                <span class="font-medium">{{ $raffle->lottery->name }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="text-center">{{ $raffle->raffle_date->format('Y-m-d') }}</flux:table.cell>

                        <flux:table.cell class="text-center font-medium">{{ $raffle->raffle_time }}</flux:table.cell>

                        <flux:table.cell class="text-center text-sm text-gray-600 dark:text-gray-400">
                            {{ $raffle->stop_sale_time }}
                        </flux:table.cell>

                        <flux:table.cell class="text-center">
                            <div x-data="{
                                                    remaining: '',
                                                    targetTime: null,
                                                    init() {
                                                        const raffleDate = '{{ $raffle->raffle_date->format('Y-m-d') }}';
                                                        const stopTime = '{{ $raffle->stop_sale_time }}';
                                                        this.targetTime = new Date(raffleDate + ' ' + stopTime).getTime();
                                                        this.updateRemaining();
                                                        setInterval(() => this.updateRemaining(), 1000);
                                                    },
                                                    updateRemaining() {
                                                        const now = new Date().getTime();
                                                        const distance = this.targetTime - now;

                                                        if (distance < 0) {
                                                            this.remaining = '{{ __('Expired') }}';
                                                            return;
                                                        }

                                                        const hours = Math.floor(distance / (1000 * 60 * 60));
                                                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                                        this.remaining = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                                                    }
                                                }" x-text="remaining" class="font-mono text-sm font-semibold"
                                :class="remaining === '{{ __('Expired') }}' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400'">
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <flux:badge size="sm" :color="$raffle?->status?->getColor()" inset="top bottom">
                                {{ $raffle?->status?->getLabel() }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <livewire:create-ticket />
    <livewire:random-bets />
    <livewire:random-bets-with-seeds />
</div>