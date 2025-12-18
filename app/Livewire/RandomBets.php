<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Lottery;
use Livewire\Component;

class RandomBets extends Component
{

    public $lotteries;
    public $games;
    public $qty;
    public $stake_amount;

    public function render()
    {
        $this->lotteries = Lottery::all();
        $this->games = Game::all();

        return view('livewire.random-bets', [
            'lotteries' => $this->lotteries,
            'games' => $this->games,
        ]);
    }
}
