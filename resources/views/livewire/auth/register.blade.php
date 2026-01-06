<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <!-- Header with Gradient Text -->
        <div class="text-center space-y-2">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                {{ __('Join DreamBet') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('Create your account and start playing today') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Personal Information -->
            <div class="space-y-4">
                <h3
                    class="text-sm font-semibold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Personal Information') }}
                </h3>

                <!-- Name -->
                <div class="space-y-2">
                    <flux:input name="name" :label="__('Full Name')" :value="old('name')" type="text" required autofocus
                        autocomplete="name" :placeholder="__('John Doe')"
                        class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                    <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                        autocomplete="email" placeholder="email@example.com"
                        class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <flux:input name="phone" :label="__('Phone Number')" :value="old('phone')" type="tel" required
                        autocomplete="tel" :placeholder="__('+1 234 567 8900')"
                        class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />
                </div>

                <!-- Birth Date -->
                <div class="space-y-2">
                    <flux:input name="birth_date" :label="__('Date of Birth')" :value="old('birth_date')" type="date"
                        required max="{{ now()->subYears(18)->format('Y-m-d') }}" :placeholder="__('YYYY-MM-DD')"
                        class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />
                </div>
            </div>

            <!-- Account Security -->
            <div class="space-y-4">
                <h3
                    class="text-sm font-semibold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Account Security') }}
                </h3>

                <!-- Password -->
                <div class="space-y-2">
                    <flux:input name="password" :label="__('Password')" type="password" required
                        autocomplete="new-password" :placeholder="__('Password')" viewable
                        class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required
                        autocomplete="new-password" :placeholder="__('Confirm password')" viewable
                        class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />
                </div>
            </div>

            <!-- Terms and Conditions Acceptance -->
            <div class="space-y-2 pt-2">
                <label class="flex items-start gap-3">
                    <flux:checkbox name="terms_accepted" value="1" required />
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        {{ __('I accept the') }}
                        <a href="{{ route('terms-and-conditions') }}" target="_blank"
                            class="text-blue-600 hover:text-purple-600 font-semibold transition-colors">
                            {{ __('Terms and Conditions') }}
                        </a>
                    </span>
                </label>
            </div>

            <!-- Register Button -->
            <div class="flex items-center justify-end pt-2">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    data-test="register-user-button">
                    {{ __('Create account') }}
                </button>
            </div>
        </form>

        <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <span class="text-gray-600 dark:text-gray-400">{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate
                class="ml-1 text-blue-600 hover:text-purple-600 font-semibold transition-colors">
                {{ __('Log in') }}
            </flux:link>
        </div>
    </div>
</x-layouts.auth>