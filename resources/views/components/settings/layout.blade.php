<div class="flex items-start gap-8 max-md:flex-col">
    <!-- Sidebar Navigation -->
    <div class="w-full md:w-64 shrink-0">
        <div class="rounded-xl bg-white p-4 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
            <div class="mb-4 px-2">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('Settings Menu') }}
                </h3>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" wire:navigate
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-zinc-800' }}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span class="font-medium">{{ __('Profile') }}</span>
                </a>

                <a href="{{ route('user-password.edit') }}" wire:navigate
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all {{ request()->routeIs('user-password.edit') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-zinc-800' }}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                    </svg>
                    <span class="font-medium">{{ __('Password') }}</span>
                </a>

                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <a href="{{ route('two-factor.show') }}" wire:navigate
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all {{ request()->routeIs('two-factor.show') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-zinc-800' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                        </svg>
                        <span class="font-medium">{{ __('Two-Factor Auth') }}</span>
                    </a>
                @endif

                <a href="{{ route('appearance.edit') }}" wire:navigate
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all {{ request()->routeIs('appearance.edit') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-zinc-800' }}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                    </svg>
                    <span class="font-medium">{{ __('Appearance') }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 min-w-0">
        <div class="rounded-xl bg-white p-6 shadow-md dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700">
            <!-- Section Header -->
            <div class="mb-6 border-b border-gray-200 pb-4 dark:border-zinc-700">
                <flux:heading class="text-gray-900 dark:text-white">{{ $heading ?? '' }}</flux:heading>
                <flux:subheading class="text-gray-600 dark:text-gray-400">{{ $subheading ?? '' }}</flux:subheading>
            </div>

            <!-- Content -->
            <div class="w-full max-w-2xl">
                {{ $slot }}
            </div>
        </div>
    </div>

    @include('components.flash-message')
</div>