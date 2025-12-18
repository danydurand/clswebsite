<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Event;
use Livewire\Component;

class CreateSportBet extends Component
{
    public $events;
    public $questions;

    public function render()
    {
        $this->events = Event::all();
        $this->questions = [];

        return view('livewire.create-sport-bet', [
            'events' => $this->events,
            'questions' => $this->questions,
        ]);
    }
}
