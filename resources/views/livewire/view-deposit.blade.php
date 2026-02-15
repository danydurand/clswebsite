<div class="w-full">
    {{-- Header --}}
    <div class="relative mb-8 overflow-hidden rounded-xl bg-blue-600 p-8 shadow-lg">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1" class="text-white">{{ __('View Deposit') }}</flux:heading>
                <flux:subheading size="lg" class="text-blue-100">{{ __('Deposit Details') }}</flux:subheading>
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
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-blue-500 opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-blue-700 opacity-20 blur-3xl"></div>
    </div>

    <div class="grid gap-6">
        {{-- General Information --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('General Information') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:label>{{ __('Transaction Code') }}</flux:label>
                    <div class="mt-1 font-mono text-lg font-semibold">{{ $deposit->trx }}</div>
                </div>

                <div>
                    <flux:label>{{ __('Created At') }}</flux:label>
                    <div class="mt-1">{{ $deposit->created_at->format('Y-m-d H:i:s') }}</div>
                </div>

                <div>
                    <flux:label>{{ __('Gateway') }}</flux:label>
                    <div class="mt-1 flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white text-xs font-bold">
                            {{ substr($deposit->gateway?->name ?? 'N/A', 0, 2) }}
                        </div>
                        <span class="font-medium">{{ $deposit->gateway?->name ?? __('Unknown') }}</span>
                    </div>
                </div>

                <div>
                    <flux:label>{{ __('Status') }}</flux:label>
                    <div class="mt-1">
                        <flux:badge :color="$deposit->status->getColor()">
                            {{ $deposit->status->getLabel() }}
                        </flux:badge>
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- Amounts --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Amounts') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <flux:label>{{ __('Amount') }}</flux:label>
                    <div class="mt-1 text-2xl font-bold text-green-600">
                        ${{ number_format($deposit->amount, 2) }}
                    </div>
                </div>

                <div>
                    <flux:label>{{ __('Charge') }}</flux:label>
                    <div class="mt-1 text-2xl font-bold text-gray-600">
                        ${{ number_format($deposit->charge, 2) }}
                    </div>
                </div>

                <div>
                    <flux:label>{{ __('Final Amount') }}</flux:label>
                    <div class="mt-1 text-2xl font-bold text-blue-600">
                        ${{ number_format($deposit->final_amount, 2) }}
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- Admin Feedback (if exists) --}}
        @if($deposit->admin_feedback)
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Admin Feedback') }}</flux:heading>
                <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                    <p class="text-gray-700">{{ $deposit->admin_feedback }}</p>
                </div>
            </flux:card>
        @endif

        {{-- Payment Details (for manual deposits) --}}
        @if(!empty($formFields))
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Payment Details') }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($formFields as $field)
                        @php
                            $fieldName = $field['name'];
                            $fieldValue = $deposit->detail['form_data'][$fieldName] ?? null;
                        @endphp

                        @if($fieldValue !== null)
                            <div class="{{ isset($field['width']) && $field['width'] === 'full' ? 'md:col-span-2' : '' }}">
                                <flux:label>{{ $field['label'] ?? ucfirst(str_replace('_', ' ', $fieldName)) }}</flux:label>
                                <div
                                    class="mt-1 rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-3 border border-gray-200 dark:border-gray-700">
                                    @if($field['type'] === 'file' && is_string($fieldValue))
                                        {{-- Display file link/image --}}
                                        @if(str_starts_with($fieldValue, 'http'))
                                            <a href="{{ $fieldValue }}" target="_blank" class="text-blue-600 hover:underline">
                                                {{ __('View File') }}
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $fieldValue) }}" target="_blank"
                                                class="text-blue-600 hover:underline">
                                                {{ basename($fieldValue) }}
                                            </a>
                                        @endif
                                    @elseif($field['type'] === 'textarea')
                                        <div class="whitespace-pre-wrap">{{ $fieldValue }}</div>
                                    @else
                                        {{ $fieldValue }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </flux:card>
        @endif

        {{-- Payment Proof (if exists) --}}
        @if(isset($deposit->detail['proof_file']) && $deposit->detail['proof_file'])
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Payment Proof') }}</flux:heading>

                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                    @php
                        $proofPath = $deposit->detail['proof_file'];
                        $extension = pathinfo($proofPath, PATHINFO_EXTENSION);
                    @endphp

                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        {{-- Display image --}}
                        <img src="{{ asset('storage/' . $proofPath) }}" alt="{{ __('Payment Proof') }}"
                            class="max-w-full h-auto rounded-lg shadow-md">
                    @else
                        {{-- Display file link --}}
                        <a href="{{ asset('storage/' . $proofPath) }}" target="_blank"
                            class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('Download Payment Proof') }}
                        </a>
                    @endif
                </div>
            </flux:card>
        @endif

        {{-- Action Buttons --}}
        <div class="flex gap-4">
            @if($deposit->status === App\Domain\Deposit\DepositStatusEnum::Pending)
                <a href="{{ route('deposits.edit', $deposit->id) }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-lg bg-green-600 px-6 py-3 text-white font-medium transition-colors hover:bg-green-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
            @endif

            <a href="{{ route('deposits.index') }}" wire:navigate
                class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-6 py-3 text-white font-medium transition-colors hover:bg-gray-700">
                {{ __('Back to Deposits') }}
            </a>
        </div>
    </div>
</div>