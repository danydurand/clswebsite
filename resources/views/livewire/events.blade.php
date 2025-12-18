<div class="relative mb-6 w-full mx-auto">
    <flux:heading size="xl" level="1">{{ __('Events') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Choose the event you want to bet on') }}</flux:subheading>
    <flux:separator variant="subtle" />

    @session('success')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-green-600 text-white text-sm p-4 rounded shadow-lg" role="alert">
            <p>{{ $value }}</p>
        </div>
    @endsession()

    <flux:modal.trigger name="create-sport-bet">
        <flux:button class="mt-4 w-5/7" variant="primary" color="blue">Create Sport Bet</flux:button>
    </flux:modal.trigger>

    <table class="table-auto w-5/7 bg-slate-100 shadow-md rounded-lg mt-5">
        <thead class="bg-slate-200">
            <tr class="rounded-lg">
                <th class="px-4 py-2">Category</th>
                <th class="px-4 py-2 text-center">Home participant</th>
                <th class="px-4 py-2 text-center">Away participant</th>
                <th class="px-4 py-2 text-center">Status</th>
                <th class="px-4 py-2 text-center">Start Time</th>
                <th class="px-4 py-2 text-center">Bet Start Time</th>
                <th class="px-4 py-2 text-center">Bet End Time</th>
                <th class="px-4 py-2 text-center">Locked?</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $event->category->name }}</td>
                    <td class="px-4 py-2 text-center">{{ $event->homeParticipant->name }}</td>
                    <td class="px-4 py-2 text-center">{{ $event->awayParticipant->name }}</td>
                    <td class="px-4 py-2 text-center">{{ $event->status_code?->description }}</td>
                    <td class="px-4 py-2 text-center">{{ $event->start_time }}</td>
                    <td class="px-4 py-2 text-center">{{ $event->bet_start_time }}</td>
                    <td class="px-4 py-2 text-center">{{ $event->bet_end_time }}</td>
                    <td class="px-4 py-2 text-center">{{ $event->is_locked ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="w-5/7 mt-4">
        {{ $events->links() }}
    </div>


    {{-- <flux:modal name="delete-note" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Note?</flux:heading>

                <flux:text class="mt-2 text-center">
                    Are you sure you want to delete the note with the title:
                    <br><br>
                    <strong>{{ $event->title }}</strong>
                    <br><br>
                    This action cannot be reversed.
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger" wire:click="deleteNote()">Delete Note
                </flux:button>
            </div>
        </div>
    </flux:modal> --}}

    <livewire:create-sport-bet />
    {{-- <livewire:edit-note /> --}}

</div>