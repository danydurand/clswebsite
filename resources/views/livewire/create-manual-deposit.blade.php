<div>
    <flux:card>
        <flux:heading size="lg">{{ __('Manual Deposit') }}</flux:heading>

        <form wire:submit="save" class="space-y-6">
            {{-- Gateway Selection --}}
            <flux:select wire:model.live="gateway_currency_id" label="{{ __('Payment Method') }}"
                placeholder="{{ __('Select a payment method') }}">
                @foreach($manualGateways as $gateway)
                    <option value="{{ $gateway->id }}">
                        {{ $gateway->gateway->name }} ({{ $gateway->currency }})
                    </option>
                @endforeach
            </flux:select>

            @if($selectedGateway)
                {{-- Instructions --}}
                @if($selectedGateway->gateway->description)
                    <flux:card variant="outline">
                        <flux:heading size="sm">{{ __('Payment Instructions') }}</flux:heading>
                        <div class="prose prose-sm max-w-none">
                            {!! nl2br(e($selectedGateway->gateway->description)) !!}
                        </div>
                    </flux:card>
                @endif

                {{-- Amount --}}
                <flux:input wire:model.live="amount" type="number" step="0.01" label="{{ __('Amount') }}"
                    placeholder="0.00" />

                <flux:text variant="muted">
                    {{ __('Limits') }}: {{ $selectedGateway->symbol }}{{ number_format($selectedGateway->min_amount, 2) }} -
                    {{ $selectedGateway->symbol }}{{ number_format($selectedGateway->max_amount, 2) }}
                </flux:text>

                {{-- Charge Summary --}}
                @if($amount > 0)
                    <flux:card variant="outline">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>{{ __('Amount') }}:</span>
                                <span>{{ $selectedGateway->symbol }}{{ number_format($amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>{{ __('Processing Charge') }}:</span>
                                <span>{{ $selectedGateway->symbol }}{{ number_format($charge, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg">
                                <span>{{ __('Total to Pay') }}:</span>
                                <span>{{ $selectedGateway->symbol }}{{ number_format($final_amount, 2) }}</span>
                            </div>
                        </div>
                    </flux:card>
                @endif

                {{-- Dynamic Form Fields --}}
                @php
                    $formFields = $selectedGateway->gateway->getFormFields();
                @endphp

                @if(count($formFields) > 0)
                    <flux:separator />
                    <flux:heading size="sm">{{ __('Payment Information') }}</flux:heading>

                    @foreach($formFields as $field)
                        @if($field['type'] === 'text')
                            <flux:input wire:model="form_data.{{ $field['name'] }}" label="{{ $field['label'] }}"
                                placeholder="{{ $field['placeholder'] ?? '' }}" :required="$field['required'] ?? false" />
                        @elseif($field['type'] === 'textarea')
                            <flux:textarea wire:model="form_data.{{ $field['name'] }}" label="{{ $field['label'] }}"
                                placeholder="{{ $field['placeholder'] ?? '' }}" :required="$field['required'] ?? false" rows="3" />
                        @elseif($field['type'] === 'date')
                            <flux:input type="date" wire:model="form_data.{{ $field['name'] }}" label="{{ $field['label'] }}"
                                :required="$field['required'] ?? false" />
                        @elseif($field['type'] === 'number')
                            <flux:input type="number" step="0.01" wire:model="form_data.{{ $field['name'] }}"
                                label="{{ $field['label'] }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                                :required="$field['required'] ?? false" />
                        @endif
                    @endforeach
                @endif

                {{-- Proof File Upload --}}
                <flux:separator />
                <flux:heading size="sm">{{ __('Payment Proof') }}</flux:heading>

                <div>
                    <flux:label>{{ __('Upload Payment Proof') }} <span class="text-red-500">*</span></flux:label>
                    <input type="file" wire:model="proof_file" accept="image/*,application/pdf" class="block w-full text-sm text-zinc-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-zinc-100 file:text-zinc-700
                                hover:file:bg-zinc-200
                                dark:file:bg-zinc-800 dark:file:text-zinc-300
                                dark:hover:file:bg-zinc-700" />
                    <flux:text variant="muted" class="mt-1">
                        {{ __('Accepted formats: JPG, PNG, PDF. Max size: 2MB') }}
                    </flux:text>
                </div>

                @if($proof_file)
                    <flux:text variant="success">
                        ✓ {{ __('File selected') }}: {{ $proof_file->getClientOriginalName() }}
                    </flux:text>
                @endif

                @error('proof_file')
                    <flux:text variant="danger">{{ $message }}</flux:text>
                @enderror
            @endif

            {{-- Actions --}}
            <div class="flex gap-3 pt-4">
                <flux:button type="submit" variant="primary" :disabled="!$selectedGateway">
                    {{ __('Submit Deposit') }}
                </flux:button>
                <flux:button type="button" variant="ghost" wire:navigate href="{{ route('deposits.index') }}">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>