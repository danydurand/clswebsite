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

                {{-- @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <a href="{{ route('two-factor.show') }}" wire:navigate
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all {{ request()->routeIs('two-factor.show') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-zinc-800' }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                        </svg>
                        <span class="font-medium">{{ __('Two-Factor Auth') }}</span>
                    </a>
                @endif --}}

                <a href="{{ route('appearance.edit') }}" wire:navigate
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all {{ request()->routeIs('appearance.edit') ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-zinc-800' }}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                    </svg>
                    <span class="font-medium">{{ __('Appearance') }}</span>
                </a>

                <!-- Language Switcher -->
                <div class="relative group">
                    <button
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-gray-700 transition-all hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-zinc-800">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12.87 15.07l-2.54-2.51.03-.03c1.74-1.94 2.98-4.17 3.71-6.53H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z" />
                        </svg>
                        <span class="flex-1 text-left font-medium">{{ __('Language') }}</span>
                        <span
                            class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ app()->getLocale() }}</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div
                        class="absolute left-0 top-full mt-1 w-full rounded-lg border border-gray-200 bg-white opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all shadow-xl dark:border-zinc-700 dark:bg-zinc-800 z-10">
                        <a href="{{ route('lang.switch', 'en') }}"
                            class="flex items-center gap-2 rounded-t-lg px-4 py-2.5 text-sm transition-colors {{ app()->getLocale() === 'en' ? 'bg-gradient-to-r from-blue-50 to-purple-50 font-semibold text-blue-700 dark:from-blue-950/20 dark:to-purple-950/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:text-gray-300 dark:hover:bg-zinc-700' }}">
                            <span class="text-lg">🇺🇸</span>
                            <span>{{ __('English') }}</span>
                        </a>
                        <a href="{{ route('lang.switch', 'es') }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors {{ app()->getLocale() === 'es' ? 'bg-gradient-to-r from-blue-50 to-purple-50 font-semibold text-blue-700 dark:from-blue-950/20 dark:to-purple-950/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:text-gray-300 dark:hover:bg-zinc-700' }}">
                            <span class="text-lg">🇪🇸</span>
                            <span>{{ __('Spanish') }}</span>
                        </a>
                        <a href="{{ route('lang.switch', 'fr') }}"
                            class="flex items-center gap-2 rounded-b-lg px-4 py-2.5 text-sm transition-colors {{ app()->getLocale() === 'fr' ? 'bg-gradient-to-r from-blue-50 to-purple-50 font-semibold text-blue-700 dark:from-blue-950/20 dark:to-purple-950/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:text-gray-300 dark:hover:bg-zinc-700' }}">
                            <span class="text-lg">🇫🇷</span>
                            <span>{{ __('French') }}</span>
                        </a>
                    </div>
                </div>
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