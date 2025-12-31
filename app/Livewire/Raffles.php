<?php

namespace App\Livewire;

use App\Models\Raffle;
use Livewire\Component;
use Livewire\WithPagination;

class Raffles extends Component
{

    use WithPagination;

    public $sortBy = 'raffle_date';
    public $sortDirection = 'desc';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function raffles()
    {
        return Raffle::query()
            ->available()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->take(10)
            ->paginate(5);
    }


    public function render()
    {

        return view('livewire.raffles', [
            'raffles' => $this->raffles,
        ]);
    }


}
