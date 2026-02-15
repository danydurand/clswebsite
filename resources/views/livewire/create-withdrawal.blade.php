<div class="max-w-7xl mx-auto space-y-6">
    {{-- Flash Messages --}}
    @include('components.flash-message')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('New Withdrawal') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Select a method and enter the amount you wish to withdraw') }}
            </p>
        </div>

        <a href="{{ route('withdrawals') }}" wire:navigate
            class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2.5 text-white font-medium transition-colors hover:bg-gray-700">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back to Withdrawals') }}
        </a>
    </div>

    {{-- Main Form --}}
    <form wire:submit="submit">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Column: Withdrawal Methods --}}
            <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('Withdrawal Methods') }}
                </h2>

                <div class="space-y-3">
                    @foreach ($methods as $method)
                        <label
                            class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all {{ $selectedMethodId == $method->id ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-zinc-700 hover:border-green-300 dark:hover:border-green-700' }}">
                            <div class="flex items-center space-x-3">
                                <input type="radio" wire:model.live="selectedMethodId" value="{{ $method->id }}"
                                    class="h-4 w-4 text-green-600 focus:ring-green-500">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $method->name }}
                                    </div>
                                    @if ($method->description)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $method->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Right Column: Amount and Calculation --}}
            <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('Withdrawal Information') }}
                </h2>

                {{-- Current Balance --}}
                <div
                    class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="text-sm text-blue-800 dark:text-blue-200 mb-1">
                        {{ __('Current Balance') }}
                    </div>
                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        ${{ number_format($customerBalance, 2) }}
                    </div>
                </div>

                {{-- Amount Input --}}
                <div class="mb-6">
                    <flux:label>{{ __('Withdrawal Amount') }}</flux:label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                        <flux:input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="amount"
                            class="pl-8" placeholder="0.00" />
                    </div>
                </div>

                @if ($selectedMethod)
                    {{-- Limits --}}
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            {{ __('Limits') }}
                        </div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            ${{ number_format($selectedMethod->min_limit, 2) }} -
                            ${{ number_format($selectedMethod->max_limit, 2) }}
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200 dark:border-zinc-700">

                    {{-- Calculation Details --}}
                    <div class="space-y-3">
                        {{-- Processing Charge --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Processing Charge') }}
                            </span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">
                                -${{ number_format($calculation['charge'], 2) }}
                            </span>
                        </div>

                        {{-- Charge Info --}}
                        @if ($selectedMethod->fixed_charge > 0 || $selectedMethod->percent_charge > 0)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @if ($selectedMethod->fixed_charge > 0 && $selectedMethod->percent_charge > 0)
                                    {{ number_format($selectedMethod->percent_charge, 2) }}% +
                                    ${{ number_format($selectedMethod->fixed_charge, 2) }} {{ __('fixed') }}
                                @elseif ($selectedMethod->fixed_charge > 0)
                                    ${{ number_format($selectedMethod->fixed_charge, 2) }} {{ __('fixed') }}
                                @else
                                    {{ number_format($selectedMethod->percent_charge, 2) }}%
                                @endif
                            </div>
                        @endif

                        <hr class="border-gray-200 dark:border-zinc-700">

                        {{-- You Will Receive --}}
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ __('You Will Receive') }}
                            </span>
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                ${{ number_format($calculation['after_charge'], 2) }}
                            </span>
                        </div>

                        {{-- Conversion (if different currency) --}}
                        @if ($selectedMethod->currency && $selectedMethod->currency !== 'USD')
                            <div
                                class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                <div class="text-xs text-amber-800 dark:text-amber-200 mb-1">
                                    {{ __('Conversion Rate') }}
                                </div>
                                <div class="text-sm font-medium text-amber-900 dark:text-amber-100">
                                    1 USD = {{ number_format($selectedMethod->rate, 2) }} {{ $selectedMethod->currency }}
                                </div>
                                <div class="text-lg font-bold text-amber-900 dark:text-amber-100 mt-2">
                                    {{ number_format($calculation['final_amount'], 2) }} {{ $selectedMethod->currency }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-6">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-3 text-white font-medium transition-colors hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            @if ($amount <= 0 || $calculation['after_charge'] <= 0) disabled @endif>
                            {{ __('Confirm Withdrawal') }}
                        </button>
                    </div>

                    {{-- Warning --}}
                    <div
                        class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                        <div class="flex items-start space-x-2">
                            <svg class="h-5 w-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-sm text-amber-800 dark:text-amber-200">
                                {{ __('The balance will be deducted from your account') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>