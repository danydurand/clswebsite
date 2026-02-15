<div class="w-full">
    {{-- Header --}}
    <div class="relative mb-8 overflow-hidden rounded-xl bg-green-600 p-8 shadow-lg">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1" class="text-white">{{ __('Edit Deposit') }}</flux:heading>
                <flux:subheading size="lg" class="text-green-100">{{ __('Update deposit information') }}
                </flux:subheading>
            </div>
            <a href="{{ route('deposits.view', $deposit->id) }}" wire:navigate
                class="rounded-lg bg-white/20 px-4 py-2 text-white backdrop-blur-sm transition-all hover:bg-white/30">
                <svg class="inline h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Cancel') }}
            </a>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-green-500 opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-green-700 opacity-20 blur-3xl"></div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit="update">
        <div class="grid gap-6">
            {{-- Basic Information --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Deposit Information') }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Transaction Code (Read-only) --}}
                    <div>
                        <flux:label>{{ __('Transaction Code') }}</flux:label>
                        <div class="mt-1 font-mono text-lg font-semibold text-gray-600">{{ $deposit->trx }}</div>
                    </div>

                    {{-- Gateway (Read-only) --}}
                    <div>
                        <flux:label>{{ __('Gateway') }}</flux:label>
                        <div class="mt-1 flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-600 text-white text-xs font-bold">
                                {{ substr($deposit->gateway?->name ?? 'N/A', 0, 2) }}
                            </div>
                            <span class="font-medium">{{ $deposit->gateway?->name ?? __('Unknown') }}</span>
                        </div>
                    </div>

                    {{-- Amount (Editable) --}}
                    <div>
                        <flux:label>{{ __('Amount') }}</flux:label>
                        <flux:input type="number" step="0.01" min="0.01" wire:model="amount" required />
                        @error('amount')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    {{-- Status (Read-only) --}}
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

            {{-- Dynamic Form Fields (for manual deposits) --}}
            @if(!empty($formFields))
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Payment Details') }}</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($formFields as $field)
                            <div class="{{ isset($field['width']) && $field['width'] === 'full' ? 'md:col-span-2' : '' }}">
                                <flux:label>
                                    {{ $field['label'] ?? ucfirst($field['name']) }}
                                    @if(isset($field['required']) && $field['required'])
                                        <span class="text-red-500">*</span>
                                    @endif
                                </flux:label>

                                @if($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'number')
                                    <flux:input type="{{ $field['type'] }}" wire:model="formData.{{ $field['name'] }}"
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        :required="isset($field['required']) && $field['required']" />
                                @elseif($field['type'] === 'textarea')
                                    <flux:textarea wire:model="formData.{{ $field['name'] }}"
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        :required="isset($field['required']) && $field['required']" rows="3" />
                                @elseif($field['type'] === 'file')
                                    {{-- Show current file if exists --}}
                                    @if(isset($formData[$field['name']]) && is_string($formData[$field['name']]))
                                        <div
                                            class="mb-3 rounded-lg bg-gray-50 dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('Current file') }}:
                                                    </p>
                                                    @php
                                                        $filePath = $formData[$field['name']];
                                                        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                                    @endphp

                                                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                        {{-- Show image preview --}}
                                                        <img src="{{ asset('storage/' . $filePath) }}" alt="{{ __('Current file') }}"
                                                            class="max-w-xs h-auto rounded-lg shadow-sm">
                                                    @else
                                                        {{-- Show file name --}}
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ basename($filePath) }}
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Remove button --}}
                                                <button type="button" wire:click="removeFile('{{ $field['name'] }}')"
                                                    class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-sm text-white font-medium transition-colors hover:bg-red-700">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    {{ __('Remove') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- File input for new upload --}}
                                    <flux:input type="file" wire:model="formData.{{ $field['name'] }}" accept="image/*" />

                                    {{-- Show loading indicator when uploading --}}
                                    <div wire:loading wire:target="formData.{{ $field['name'] }}" class="mt-2">
                                        <p class="text-sm text-blue-600">{{ __('Uploading...') }}</p>
                                    </div>
                                @endif

                                @error('formData.' . $field['name'])
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif

            {{-- Payment Proof (for manual deposits) --}}
            @if(!empty($formFields))
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Payment Proof') }}</flux:heading>

                    {{-- Show current proof file if exists --}}
                    @if($current_proof_file)
                        <div
                            class="mb-4 rounded-lg bg-gray-50 dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Current file') }}:
                                    </p>
                                    @php
                                        $extension = pathinfo($current_proof_file, PATHINFO_EXTENSION);
                                    @endphp

                                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        {{-- Show image preview --}}
                                        <img src="{{ asset('storage/' . $current_proof_file) }}" alt="{{ __('Payment Proof') }}"
                                            class="max-w-xs h-auto rounded-lg shadow-sm">
                                    @else
                                        {{-- Show file name --}}
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ basename($current_proof_file) }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Remove button --}}
                                <button type="button" wire:click="removeProofFile"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-sm text-white font-medium transition-colors hover:bg-red-700">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- File input for new upload --}}
                    <div>
                        <flux:label>{{ __('Upload Payment Proof') }}</flux:label>
                        <flux:input type="file" wire:model="proof_file" accept="image/*,application/pdf" />
                        <p class="mt-1 text-sm text-gray-500">{{ __('Accepted formats: JPG, PNG, PDF. Max size: 2MB') }}</p>

                        {{-- Show loading indicator when uploading --}}
                        <div wire:loading wire:target="proof_file" class="mt-2">
                            <p class="text-sm text-blue-600">{{ __('Uploading...') }}</p>
                        </div>
                    </div>
                </flux:card>
            @endif

            {{-- Action Buttons --}}
            <div class="flex gap-4">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-green-600 px-6 py-3 text-white font-medium transition-colors hover:bg-green-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Save Changes') }}
                </button>

                <a href="{{ route('deposits.view', $deposit->id) }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-6 py-3 text-white font-medium transition-colors hover:bg-gray-700">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </form>
</div>