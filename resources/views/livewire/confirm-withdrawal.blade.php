<div class="max-w-4xl mx-auto space-y-6">
    {{-- Flash Messages --}}
    @include('components.flash-message')

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ __('Withdrawal Confirmation') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Please provide the required information') }}
        </p>
    </div>

    {{-- Withdrawal Summary --}}
    <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('Withdrawal Summary') }}
        </h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Method') }}</div>
                <div class="font-medium text-gray-900 dark:text-white">{{ $withdrawal->withdrawMethod->name }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Transaction Code') }}</div>
                <code
                    class="text-sm font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded">{{ $withdrawal->trx }}</code>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Requested') }}</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    ${{ number_format($withdrawal->amount, 2) }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Processing Charge') }}</div>
                <div class="text-lg font-semibold text-red-600 dark:text-red-400">
                    -${{ number_format($withdrawal->charge, 2) }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('You Will Receive') }}</div>
                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                    ${{ number_format($withdrawal->after_charge, 2) }}</div>
            </div>

            @if ($withdrawal->currency && $withdrawal->currency !== 'USD')
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Final Amount') }}</div>
                    <div class="text-lg font-bold text-green-600 dark:text-green-400">
                        {{ number_format($withdrawal->final_amount, 2) }} {{ $withdrawal->currency }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Dynamic Form --}}
    <form wire:submit="submit">
        <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('Withdrawal Information') }}
            </h2>

            @if ($withdrawal->withdrawMethod->form)
                <div class="space-y-4">
                    @foreach ($withdrawal->withdrawMethod->form->form_data ?? [] as $field)
                        <div>
                            <flux:label>
                                {{ $field['label'] ?? $field['name'] }}
                                @if ($field['required'] ?? false)
                                    <span class="text-red-600">*</span>
                                @endif
                            </flux:label>

                            @if ($field['type'] === 'text' || $field['type'] === 'email')
                                <flux:input type="{{ $field['type'] }}" wire:model="withdrawInfo.{{ $field['name'] }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}" />
                            @elseif ($field['type'] === 'textarea')
                                <flux:textarea wire:model="withdrawInfo.{{ $field['name'] }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}" rows="3" />
                            @elseif ($field['type'] === 'select')
                                <flux:select wire:model="withdrawInfo.{{ $field['name'] }}">
                                    <option value="">{{ __('Select') }}...</option>
                                    @foreach ($field['options'] ?? [] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </flux:select>
                            @endif

                            @error('withdrawInfo.' . $field['name'])
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('No additional information required') }}
                </p>
            @endif
        </div>

        {{-- Warning --}}
        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
            <div class="flex items-start space-x-3">
                <svg class="h-6 w-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <div class="font-medium text-amber-900 dark:text-amber-100">
                        {{ __('Important') }}
                    </div>
                    <div class="mt-1 text-sm text-amber-800 dark:text-amber-200">
                        {{ __('The balance will be deducted from your account') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('withdrawals.create') }}" wire:navigate
                class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2.5 text-white font-medium transition-colors hover:bg-gray-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to Methods') }}
            </a>

            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-green-600 px-6 py-2.5 text-white font-medium transition-colors hover:bg-green-700">
                {{ __('Submit Withdrawal') }}
            </button>
        </div>
    </form>
</div>