<?php

namespace App\Livewire;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalTickets = 0;
    public int $winnerTickets = 0;
    public int $loserTickets = 0;

    public function mount(): void
    {
        $customerId = Auth::user()->customer->id;

        $this->totalTickets = Ticket::byCustomer($customerId)
            ->notCancelled()
            ->count();

        $this->winnerTickets = Ticket::byCustomer($customerId)
            ->winner()
            ->count();

        $this->loserTickets = Ticket::byCustomer($customerId)
            ->looser()
            ->notCancelled()
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
