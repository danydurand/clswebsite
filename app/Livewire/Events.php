<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Events extends Component
{
    use WithPagination;

    public function render()
    {
        $events = Event::orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.events', [
            'events' => $events,
        ]);
    }
}
