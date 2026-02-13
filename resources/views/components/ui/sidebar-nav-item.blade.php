@props([
    'href' => '#',
    'icon' => null,
    'label' => '',
    'active' => false,
    'badge' => null,
    'wireNavigate' => true
])

@php
    $classes = $active 
        ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md' 
        : 'text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:text-blue-600';
@endphp

<a 
    href="{{ $href }}" 
    {{ $wireNavigate ? 'wire:navigate' : '' }}
    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group {{ $classes }}"
>
    @if($icon)
        <svg class="w-5 h-5 {{ $active ? 'text-white' : 'text-gray-500 group-hover:text-blue-600' }}" fill="currentColor" viewBox="0 0 24 24">
            @if($icon === 'home')
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            @elseif($icon === 'arrow-path')
                <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
            @elseif($icon === 'trophy')
                <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
            @elseif($icon === 'ticket')
                <path d="M22 10V6c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2zm-2-1.46c-1.19.69-2 1.99-2 3.46s.81 2.77 2 3.46V18H4v-2.54c1.19-.69 2-1.99 2-3.46 0-1.48-.8-2.77-1.99-3.46L4 6h16v2.54z"/>
            @elseif($icon === 'banknotes')
                <path d="M2 6h20v2H2zm0 5h20v2H2zm0 5h20v2H2z"/>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v2h-2zm0 4h2v6h-2z"/>
            @elseif($icon === 'arrow-down-tray')
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                <path d="M13 7h-2v5H8l4 4 4-4h-3z"/>
            @elseif($icon === 'sparkles')
                <path d="M9.5 2l.5 2.5L12.5 5l-2.5.5L9.5 8 9 5.5 6.5 5 9 4.5 9.5 2zm0 14l.5 2.5 2.5.5-2.5.5-.5 2.5-.5-2.5L6.5 19l2.5-.5.5-2.5zM19 9l-1.26-2.74L15 5l2.74-1.25L19 1l1.25 2.75L23 5l-2.75 1.26L19 9z"/>
            @elseif($icon === 'cog')
                <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
            @endif
        </svg>
    @endif
    
    <span class="flex-1 font-medium">{{ $label }}</span>
    
    @if($badge)
        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $active ? 'bg-white/20 text-white' : 'bg-gradient-to-r from-blue-600 to-purple-600 text-white' }}">
            {{ $badge }}
        </span>
    @endif
</a>
