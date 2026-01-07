@props([
    'name' => '',
    'initials' => '',
    'image' => null,
    'size' => 'md',
    'gradient' => true
])
@php
    $sizeClasses = [
        'sm' => 'h-8 w-8 text-sm',
        'md' => 'h-10 w-10 text-base',
        'lg' => 'h-12 w-12 text-lg',
        'xl' => 'h-16 w-16 text-xl'
    ];

    $bgClasses = $gradient
        ? 'bg-gradient-to-br from-blue-600 to-purple-600 text-white'
        : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-white';
@endphp

<div class="relative flex {{ $sizeClasses[$size] }} shrink-0 overflow-hidden rounded-lg {{ $bgClasses }} items-center justify-center font-semibold shadow-md">
    @if($image)
        <img src="{{ $image }}" alt="{{ $name }}" class="h-full w-full object-cover">
    @else
        <span>{{ $initials }}</span>
    @endif
</div>
