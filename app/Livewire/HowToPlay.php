<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class HowToPlay extends Component
{
    public $ticket;
    public $userMessage = '';
    public $colorMessage = 'red';
    public $ticketDetails = [];
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public $ticket_id;
    public $ticket_code;
    public $ticket_created_at;
    public $ticket_stake_amount;
    public $ticket_won;
    public $ticket_prize;
    public $ticket_status;
    public $ticket_status_color;
    public $ticket_payment_status;

    public function mount($id)
    {
        $this->ticket = Ticket::findOrFail($id);
    }

    public function back()
    {
        return redirect()->route('tickets.edit', $this->ticket->id);
    }

    public function render()
    {
        return view('livewire.tickets.how-to-play');
    }
}
