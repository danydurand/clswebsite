<?php

namespace App\Livewire;

use App\Models\Raffle;
use Livewire\Component;
use Livewire\WithPagination;

class Raffles extends Component
{

    use WithPagination;

    public function render()
    {
        $raffles = Raffle::unavailable()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.raffles', [
            'raffles' => $raffles,
        ]);
    }

    public function putBetOn($id)
    {
        $this->dispatch('create-ticket', $id);
    }


}
