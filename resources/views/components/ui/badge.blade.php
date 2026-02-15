@props([
    'count' => 0,
    'variant' => 'primary',
    'size' => 'md'
])
@php
    $variantClasses = [
        'primary' => 'bg-gradient-to-r from-blue-600 to-purple-600 text-white',
        'success' => 'bg-gradient-to-r from-green-500 to-emerald-600 text-white',
        'warning' => 'bg-gradient-to-r from-yellow-500 to-orange-600 text-white',
        'danger' => 'bg-gradient-to-r from-red-500 to-pink-600 text-white',
        'info' => 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white'
    ];

    $sizeClasses = [
        'sm' => 'px-1.5 py-0.5 text-xs',
        'md' => 'px-2 py-0.5 text-xs',
        'lg' => 'px-2.5 py-1 text-sm'
    ];
@endphp
@if($count > 0)
    <span class="inline-flex items-center justify-center rounded-full font-semibold {{ $variantClasses[$variant] }} {{ $sizeClasses[$size] }} shadow-sm">
        {{ $count > 99 ? '99+' : $count }}
    </span>
@endif
