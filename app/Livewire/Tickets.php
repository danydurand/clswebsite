<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Tickets extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();

        $tickets = Ticket::byCustomer($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.tickets', [
            'tickets' => $tickets,
        ]);
    }
}
