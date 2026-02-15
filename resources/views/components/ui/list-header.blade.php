@props([
    'title' => '',
    'subtitle' => '',
    'gradientFrom' => 'blue-600',
    'gradientTo' => 'purple-600',
    'subtitleColor' => 'blue-100',
    'decorColor1' => 'blue-500',
    'decorColor2' => 'purple-500',
])

<!-- Gradient Header -->
<div class="relative mb-4 overflow-hidden rounded-xl bg-gradient-to-r from-{{ $gradientFrom }} to-{{ $gradientTo }} p-8 shadow-lg">
    <div class="relative z-10">
        <flux:heading size="xl" level="1" class="text-white">{{ $title }}</flux:heading>
        @if($subtitle)
            <flux:subheading size="lg" class="text-{{ $subtitleColor }}">{{ $subtitle }}</flux:subheading>
        @endif
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-{{ $decorColor1 }} opacity-20 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-{{ $decorColor2 }} opacity-20 blur-3xl"></div>
</div>
