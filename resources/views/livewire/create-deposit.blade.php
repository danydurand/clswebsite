<div class="w-full">
    {{-- Gradient Header --}}
    <div class="relative mb-8 overflow-hidden rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 p-8 shadow-lg">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1" class="text-white">{{ __('Create Deposit') }}</flux:heading>
                <flux:subheading size="lg" class="text-green-100">{{ __('Add funds to your account') }}
                </flux:subheading>
            </div>
            <a href="{{ route('deposits.index') }}" wire:navigate
                class="rounded-lg bg-white/20 px-4 py-2 text-white backdrop-blur-sm transition-all hover:bg-white/30">
                <svg class="inline h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to Deposits') }}
            </a>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-green-500 opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-emerald-500 opacity-20 blur-3xl"></div>
    </div>

    {{-- Main Form --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column: Gateway and Amount Selection --}}
                <div class="space-y-6">
                    {{-- Gateway Selection --}}
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-zinc-800/50">
                        <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ __('Select Payment Gateway') }}
                        </h4>

                        <flux:select wire:model.live="gateway_currency_id" label="{{ __('Gateway') }}"
                            placeholder="{{ __('Choose a payment method') }}">
                            @foreach ($gatewayCurrencies as $gatewayCurrency)
                                <flux:select.option wire:key="gw-{{ $gatewayCurrency->id }}"
                                    value="{{ $gatewayCurrency->id }}">
                                    {{ $gatewayCurrency->name }} ({{ $gatewayCurrency->currency }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Amount Input --}}
                    <div
                        class="rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 p-4 dark:from-green-950/20 dark:to-emerald-950/20">
                        <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ __('Deposit Amount') }}
                        </h4>

                        <flux:input wire:model.live="amount" type="number" step="0.01" min="0"
                            label="{{ __('Amount') }}" placeholder="{{ __('Enter amount') }}" />
                    </div>
                </div>

                {{-- Right Column: Information Panel --}}
                <div class="space-y-4">
                    <div
                        class="rounded-lg border-2 border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-950/20">
                        <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ __('Deposit Summary') }}
                        </h4>

                        @if($selectedGateway)
                            {{-- Limits --}}
                            <div class="mb-4 flex justify-between border-b border-green-200 pb-3 dark:border-green-800">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Limits') }}</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200">
                                    ${{ number_format($selectedGateway->min_amount, 2) }} -
                                    ${{ number_format($selectedGateway->max_amount, 2) }}
                                </span>
                            </div>

                            {{-- Processing Charge --}}
                            <div class="mb-4 flex justify-between border-b border-green-200 pb-3 dark:border-green-800">
                                <div class="flex items-center gap-1">
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Processing Charge') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-500"
                                        title="{{ __('Fixed charge + Percentage charge') }}">
                                        ({{ $selectedGateway->percent_charge }}% +
                                        ${{ number_format($selectedGateway->fixed_charge, 2) }})
                                    </span>
                                </div>
                                <span class="font-medium text-gray-800 dark:text-gray-200">
                                    ${{ number_format($charge, 2) }}
                                </span>
                            </div>

                            {{-- Total Amount --}}
                            <div class="flex justify-between pt-2">
                                <span
                                    class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('Total') }}</span>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    ${{ number_format($final_amount, 2) }}
                                </span>
                            </div>

                            {{-- Info Message --}}
                            <div class="mt-4 rounded-lg bg-blue-50 p-3 dark:bg-blue-950/20">
                                <div class="flex gap-2">
                                    <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                        {{ __('Your deposit will be reviewed and processed by an administrator. You will be notified once it is approved.') }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <p>{{ __('Select a payment gateway to see details') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between gap-4 border-t border-gray-200 pt-6 dark:border-zinc-700">
                <a href="{{ route('deposits.index') }}" wire:navigate
                    class="rounded-lg border border-gray-300 px-6 py-2.5 font-semibold text-gray-700 transition-all hover:bg-gray-50 dark:border-zinc-600 dark:text-gray-300 dark:hover:bg-zinc-800">
                    {{ __('Cancel') }}
                </a>

                <button type="submit" @if(!$selectedGateway || !$amount || $amount <= 0 || $amount < $selectedGateway?->min_amount || $amount > $selectedGateway?->max_amount) disabled @endif
                    class="rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-2.5 font-semibold text-white shadow-md transition-all hover:from-green-700 hover:to-emerald-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ __('Confirm Deposit') }}
                </button>
            </div>
        </form>
    </div>
</div>