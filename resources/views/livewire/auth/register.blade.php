<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Personal Information -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Personal Information') }}</h3>

                <!-- Name -->
                <flux:input name="name" :label="__('Full Name')" :value="old('name')" type="text" required autofocus
                    autocomplete="name" :placeholder="__('John Doe')" />

                <!-- Email Address -->
                <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                    autocomplete="email" placeholder="email@example.com" />

                <!-- Country -->
                {{-- <flux:select name="country_id" :label="__('Country')" required
                    placeholder="{{ __('Select your country') }}">
                    @foreach(\App\Models\Country::active()->orderBy('name')->get() as $country)
                    <flux:select.option value="{{ $country->id }}" :selected="old('country_id') == $country->id">
                        {{ $country->name }}
                    </flux:select.option>
                    @endforeach
                </flux:select> --}}

                <!-- Phone -->
                <flux:input name="phone" :label="__('Phone Number')" :value="old('phone')" type="tel" required
                    autocomplete="tel" :placeholder="__('+1 234 567 8900')" />

                <!-- Document ID -->
                {{--
                <flux:input name="document_id" :label="__('Document ID')" :value="old('document_id')" type="text"
                    required maxlength="20" :placeholder="__('National ID / Passport')" /> --}}

                <!-- Birth Date -->

                <flux:input name="birth_date" :label="__('Date of Birth')" :value="old('birth_date')" type="date"
                    required max="{{ now()->subYears(18)->format('Y-m-d') }}" :placeholder="__('YYYY-MM-DD')" />
            </div>

            <!-- Account Security -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Account Security') }}</h3>

                <!-- Password -->
                <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password"
                    :placeholder="__('Password')" viewable />

                <!-- Confirm Password -->
                <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required
                    autocomplete="new-password" :placeholder="__('Confirm password')" viewable />
            </div>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts.auth>