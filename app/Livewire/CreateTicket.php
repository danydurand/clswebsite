<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Lottery;
use Livewire\Component;

class CreateTicket extends Component
{

    public $lotteries;
    public $games;

    public function render()
    {
        $this->lotteries = Lottery::all();
        $this->games = Game::all();

        return view('livewire.create-ticket', [
            'lotteries' => $this->lotteries,
            'games' => $this->games,
        ]);
    }
}
