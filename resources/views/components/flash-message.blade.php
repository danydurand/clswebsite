@php
    $successMessages = session()->pull('flash_success', []);
    $errorMessages = session()->pull('flash_error', []);
    $warningMessages = session()->pull('flash_warning', []);
@endphp

{{-- Success Messages --}}
@foreach ($successMessages as $index => $message)
    @php
        $topPosition = 5 + ($index * 6); // 5rem base + 6rem spacing per message
        $successKey = 'success-' . $index . '-' . md5($message . now()->format('U.u'));
    @endphp
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full" wire:key="{{ $successKey }}"
        style="top: {{ $topPosition }}rem;"
        class="fixed right-5 z-50 flex w-full max-w-sm overflow-hidden bg-white rounded-lg shadow-md border-l-4 border-green-500">
        <div class="flex items-center justify-center w-12 bg-green-500">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold text-green-500">Success</span>
                <p class="text-sm text-gray-600">{{ $message }}</p>
            </div>
        </div>

        <button @click="show = false"
            class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 bg-transparent hover:bg-transparent">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@endforeach

{{-- Error Messages --}}
@foreach ($errorMessages as $index => $message)
    @php
        $topPosition = 5 + ((count($successMessages) + $index) * 6); // Stack after success messages
        $errorKey = 'error-' . $index . '-' . md5($message . now()->format('U.u'));
    @endphp
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full" wire:key="{{ $errorKey }}"
        style="top: {{ $topPosition }}rem;"
        class="fixed right-5 z-50 flex w-full max-w-sm overflow-hidden bg-white rounded-lg shadow-md border-l-4 border-red-500">
        <div class="flex items-center justify-center w-12 bg-red-500">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold text-red-500">Error</span>
                <p class="text-sm text-gray-600">{{ $message }}</p>
            </div>
        </div>

        <button @click="show = false"
            class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 bg-transparent hover:bg-transparent">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@endforeach

{{-- Warning Messages --}}
@foreach ($warningMessages as $index => $message)
    @php
        $topPosition = 5 + ((count($successMessages) + count($errorMessages) + $index) * 6); // Stack after success and error messages
        $warningKey = 'warning-' . $index . '-' . md5($message . now()->format('U.u'));
    @endphp
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full" wire:key="{{ $warningKey }}"
        style="top: {{ $topPosition }}rem;"
        class="fixed right-5 z-50 flex w-full max-w-sm overflow-hidden bg-white rounded-lg shadow-md border-l-4 border-yellow-500">
        <div class="flex items-center justify-center w-12 bg-yellow-500">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <div class="px-4 py-2 -mx-3">
            <div class="mx-3">
                <span class="font-semibold text-yellow-600">Warning</span>
                <p class="text-sm text-gray-600">{{ $message }}</p>
            </div>
        </div>

        <button @click="show = false"
            class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 bg-transparent hover:bg-transparent">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@endforeach