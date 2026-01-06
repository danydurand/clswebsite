<div class="relative mb-6 w-full max-w-5xl mx-auto px-4 py-8">
    <flux:heading size="xl" level="1">{{ __('Terms and Conditions') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">
        {{ __('Please read our terms and conditions carefully') }}
    </flux:subheading>
    <flux:separator variant="subtle" />

    <div class="mt-8">
        @if(count($termsBySystem) > 0)
            <!-- Three Column Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($termsBySystem as $systemData)
                    <div class="space-y-4">
                        <!-- System Section Header -->
                        <flux:heading size="lg" level="2" class="text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400 pb-2">
                            {{ $systemData['system']->name }}
                        </flux:heading>

                        <!-- Terms for this system -->
                        <div class="space-y-3 text-zinc-700 dark:text-zinc-300">
                            @foreach($systemData['terms'] as $term)
                                <div class="pl-3 border-l-2 border-zinc-200 dark:border-zinc-700">
                                    <p class="text-sm leading-relaxed">
                                        {!! str($term->text)->markdown() !!}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                    {{ __('No terms and conditions available at this time.') }}
                </flux:subheading>
            </div>
        @endif
    </div>

    <!-- Back to Register Link -->
    <div class="mt-12 text-center">
        <flux:link :href="route('register')" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline">
            {{ __('← Back to Register') }}
        </flux:link>
    </div>
</div>