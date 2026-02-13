<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ __('Withdrawal Details') }}
        </h1>

        <a href="{{ route('withdrawals') }}" wire:navigate
            class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2.5 text-white font-medium transition-colors hover:bg-gray-700">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back to Withdrawals') }}
        </a>
    </div>

    {{-- Status Badge --}}
    <div class="flex items-center space-x-3">
        @php
            $statusEnum = \App\Domain\Withdrawal\WithdrawalStatusEnum::from($withdrawal->status);
            $color = $statusEnum->getColor();
        @endphp
        <flux:badge :color="$color" size="lg">
            {{ $statusEnum->getLabel() }}
        </flux:badge>
        <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ $withdrawal->created_at->format('M d, Y h:i A') }}
        </span>
    </div>

    {{-- Withdrawal Information --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('Withdrawal Information') }}
        </h2>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Transaction Code') }}</div>
                <code
                    class="text-sm font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded">{{ $withdrawal->trx }}</code>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Method') }}</div>
                <div class="font-medium text-gray-900 dark:text-white">{{ $withdrawal->withdrawMethod->name }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Requested Amount') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    ${{ number_format($withdrawal->amount, 2) }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Processing Charge') }}</div>
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                    -${{ number_format($withdrawal->charge, 2) }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('After Charges') }}</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    ${{ number_format($withdrawal->after_charge, 2) }}</div>
            </div>

            @if ($withdrawal->currency && $withdrawal->currency !== 'USD')
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Final Amount') }}</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ number_format($withdrawal->final_amount, 2) }} {{ $withdrawal->currency }}
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Conversion Rate') }}</div>
                    <div class="font-medium text-gray-900 dark:text-white">
                        1 USD = {{ number_format($withdrawal->rate, 2) }} {{ $withdrawal->currency }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Withdrawal Details (from dynamic form) --}}
    @if ($withdrawal->withdraw_information)
        <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('Provided Information') }}
            </h2>

            <div class="grid grid-cols-2 gap-4">
                @foreach ($withdrawal->withdraw_information as $key => $value)
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}
                        </div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Admin Feedback --}}
    @if ($withdrawal->admin_feedback)
        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-6 border border-blue-200 dark:border-blue-800">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                {{ __('Admin Feedback') }}
            </h2>
            <p class="text-blue-800 dark:text-blue-200">
                {{ $withdrawal->admin_feedback }}
            </p>
        </div>
    @endif

    {{-- Timeline --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('Timeline') }}
        </h2>

        <div class="space-y-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-green-500"></div>
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">{{ __('Withdrawal Created') }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>

            @if ($withdrawal->status !== \App\Domain\Withdrawal\WithdrawalStatusEnum::Initiate->value)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-green-500"></div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ __('Withdrawal Confirmed') }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $withdrawal->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            @endif

            @if ($withdrawal->status === \App\Domain\Withdrawal\WithdrawalStatusEnum::Success->value)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-green-500"></div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ __('Withdrawal Approved') }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $withdrawal->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            @elseif ($withdrawal->status === \App\Domain\Withdrawal\WithdrawalStatusEnum::Reject->value)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-red-500"></div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ __('Withdrawal Rejected') }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $withdrawal->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>