<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <!-- Header with Gradient Text -->
        <div class="text-center space-y-2 rounded-lg h-64">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                {{ __('Welcome Back') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('Enter your credentials to access your account') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                    autofocus autocomplete="email" placeholder="email@example.com"
                    class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />
            </div>

            <!-- Password -->
            <div class="relative space-y-2">
                <flux:input name="password" :label="__('Password')" type="password" required
                    autocomplete="current-password" :placeholder="__('Password')" viewable
                    class="transition-all duration-200 focus:ring-2 focus:ring-purple-500" />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0 text-blue-600 hover:text-purple-600 transition-colors"
                        :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <!-- Login Button -->
            <div class="flex items-center justify-end pt-2">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate
                    class="ml-1 text-blue-600 hover:text-purple-600 font-semibold transition-colors">
                    {{ __('Sign up') }}
                </flux:link>
            </div>
        @endif
    </div>
</x-layouts.auth>