<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-gray-50 dark:bg-zinc-800">
    <!-- Sidebar -->
    <flux:sidebar sticky stashable class="border-e border-gray-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <!-- Gradient Header with Logo -->
        <div class="mb-6 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 p-4 shadow-lg">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3" wire:navigate>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
                    <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-white">DreamBet</div>
                    <div class="text-xs text-blue-100">Lottery System</div>
                </div>
            </a>
        </div>

        <!-- Navigation Section -->
        <div class="space-y-1 px-2">
            <div class="mb-3 px-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('Platform') }}
            </div>

            <x-ui.sidebar-nav-item :href="route('dashboard')" icon="home" :label="__('Dashboard')"
                :active="request()->routeIs('dashboard')" />

            <x-ui.sidebar-nav-item :href="route('raffles')" icon="arrow-path" :label="__('Lottery Games')"
                :active="request()->routeIs('raffles')" />

            <x-ui.sidebar-nav-item :href="route('tickets.index')" icon="ticket" :label="__('My Tickets')"
                :active="request()->routeIs('tickets*')" />

            <x-ui.sidebar-nav-item :href="route('deposits.index')" icon="banknotes" :label="__('Deposits')"
                :active="request()->routeIs('deposits*')" />

            <x-ui.sidebar-nav-item :href="route('withdrawals')" icon="arrow-down-tray" :label="__('Withdrawals')"
                :active="request()->routeIs('withdrawals*')" />

            <x-ui.sidebar-nav-item :href="route('events')" icon="trophy" :label="__('Events')"
                :active="request()->routeIs('events')" />
        </div>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <div class="hidden lg:block px-2">
            <flux:dropdown position="top" align="start" class="w-full">
                <button
                    class="w-full rounded-lg border border-gray-200 bg-white p-3 transition-all hover:border-blue-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-blue-600">
                    <div class="flex items-center gap-3">
                        <x-ui.user-avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" size="md"
                            :gradient="true" />
                        <div class="flex-1 text-left">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</div>
                        </div>
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                </button>

                <flux:menu class="w-[280px]">
                    <!-- User Info Header -->
                    <div class="border-b border-gray-100 p-3 dark:border-zinc-700">
                        <div class="flex items-center gap-3">
                            <x-ui.user-avatar :name="auth()->user()->name" :initials="auth()->user()->initials()"
                                size="lg" :gradient="true" />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                    </div>

                    <flux:menu.separator />

                    <!-- Settings -->
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>

                    <flux:menu.separator />

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full text-red-600 dark:text-red-400">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden border-b border-gray-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <!-- Mobile Logo -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
            <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600">
                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" />
                </svg>
            </div>
            <span class="font-bold text-gray-900 dark:text-white">DreamBet</span>
        </a>

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <button class="flex items-center gap-2 rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-zinc-800">
                <x-ui.user-avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" size="sm"
                    :gradient="true" />
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <flux:menu>
                <!-- User Info Header -->
                <div class="border-b border-gray-100 p-3 dark:border-zinc-700">
                    <div class="flex items-center gap-3">
                        <x-ui.user-avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" size="md"
                            :gradient="true" />
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>

                <flux:menu.separator />

                <!-- Settings -->
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full text-red-600 dark:text-red-400">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>