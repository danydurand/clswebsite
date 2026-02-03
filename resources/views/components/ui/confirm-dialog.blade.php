@props([
    'show' => false,
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmAction' => null,
    'cancelAction' => null,
    'type' => 'danger', // danger, warning, info
])

@php
    $typeColors = [
        'danger' => [
            'icon' => 'text-red-600 dark:text-red-400',
            'iconBg' => 'bg-red-100 dark:bg-red-900/20',
            'button' => 'bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700',
        ],
        'warning' => [
            'icon' => 'text-yellow-600 dark:text-yellow-400',
            'iconBg' => 'bg-yellow-100 dark:bg-yellow-900/20',
            'button' => 'bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700',
        ],
        'info' => [
            'icon' => 'text-blue-600 dark:text-blue-400',
            'iconBg' => 'bg-blue-100 dark:bg-blue-900/20',
            'button' => 'bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700',
        ],
    ];
    
    $colors = $typeColors[$type] ?? $typeColors['danger'];
@endphp

<div 
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    style="display: none;">
    
    <!-- Background overlay -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"
        @click="show = false">
    </div>

    <!-- Modal container -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-zinc-700">
            
            <!-- Modal content -->
            <div class="bg-white dark:bg-zinc-900 px-6 pb-6 pt-5">
                <div class="sm:flex sm:items-start">
                    <!-- Icon -->
                    <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full {{ $colors['iconBg'] }} sm:mx-0 sm:h-12 sm:w-12">
                        @if($type === 'danger')
                            <svg class="h-7 w-7 {{ $colors['icon'] }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        @elseif($type === 'warning')
                            <svg class="h-7 w-7 {{ $colors['icon'] }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        @else
                            <svg class="h-7 w-7 {{ $colors['icon'] }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        @endif
                    </div>
                    
                    <!-- Text content -->
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                        <h3 class="text-xl font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            {{ $title }}
                        </h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ $message }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action buttons -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                <button 
                    type="button"
                    @if($confirmAction) wire:click="{{ $confirmAction }}" @endif
                    @click="show = false"
                    class="inline-flex w-full justify-center rounded-lg {{ $colors['button'] }} px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:shadow-lg sm:w-auto">
                    {{ $confirmText }}
                </button>
                <button 
                    type="button"
                    @if($cancelAction) wire:click="{{ $cancelAction }}" @endif
                    @click="show = false"
                    class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all sm:mt-0 sm:w-auto">
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>
</div>
