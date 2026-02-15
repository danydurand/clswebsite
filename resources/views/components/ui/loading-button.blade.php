@props([
    'action' => null,
    'color' => 'blue',
    'icon' => null,
    'loadingText' => 'Loading...',
    'gradient' => false,
])

@php
    $colorClasses = [
        'blue' => $gradient 
            ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700' 
            : 'bg-blue-500 hover:bg-blue-600',
        'green' => $gradient 
            ? 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700' 
            : 'bg-green-500 hover:bg-green-600',
        'orange' => 'bg-orange-500 hover:bg-orange-600',
        'red' => $gradient 
            ? 'bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700' 
            : 'bg-red-500 hover:bg-red-600',
    ];
    
    $buttonColor = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<button 
    @if($action) wire:click="{{ $action }}" wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed" @endif
    {{ $attributes->merge(['class' => "inline-flex items-center gap-2 rounded-lg {$buttonColor} px-4 py-2.5 font-semibold text-white shadow-md transition-all hover:shadow-lg"]) }}>
    
    @if($action)
        <!-- Spinner (shown when loading) -->
        <svg wire:loading wire:target="{{ $action }}" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    
    <!-- Icon (hidden when loading) -->
    @if($icon)
        <svg @if($action) wire:loading.remove wire:target="{{ $action }}" @endif class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif
    
    <!-- Button text -->
    <span @if($action) wire:loading.remove wire:target="{{ $action }}" @endif>
        {{ $slot }}
    </span>
    
    @if($action)
        <!-- Loading text -->
        <span wire:loading wire:target="{{ $action }}">{{ $loadingText }}</span>
    @endif
</button>
