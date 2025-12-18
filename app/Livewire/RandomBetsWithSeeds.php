<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Lottery;
use Livewire\Component;

class RandomBetsWithSeeds extends Component
{

    public $lotteries;
    public $games;
    public $seeds;
    public $qty;
    public $stake_amount;

    public function render()
    {
        $this->lotteries = Lottery::all();
        $this->games = Game::all();

        return view('livewire.random-bets-with-seeds', [
            'lotteries' => $this->lotteries,
            'games' => $this->games,
        ]);
    }
}
