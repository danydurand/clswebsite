@props([
    'title' => '',
    'buttonText' => null,
    'buttonRoute' => null,
    'showButton' => true,
])

<div class="flex items-center justify-between mb-4">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        {{ $title }}
    </h1>

    @if($showButton && $buttonText && $buttonRoute)
        <a href="{{ $buttonRoute }}" wire:navigate
            class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-white font-medium transition-colors hover:bg-green-700">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ $buttonText }}
        </a>
    @endif
</div>
