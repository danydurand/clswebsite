<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Tickets extends Component
{
    use WithPagination;

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $ticket;
    public $userMessage = '';
    public $colorMessage = 'red';
    public $ticket_id;
    public $ticket_code;
    public $ticket_created_at;
    public $ticket_status;
    public $ticket_status_color;
    public $ticket_stake_amount;
    public $ticket_won;
    public $ticket_prize;

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
    public function tickets()
    {
        $user = Auth::user();
        return Ticket::query()
            ->byCustomer($user->customer_id)
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(5);
    }

    public function viewTicket($id)
    {
        // info('Viewing ticket: ' . $id);
        $this->ticket = Ticket::findOrFail($id);
        $this->ticket_id = $id;
        $this->ticket_code = $this->ticket->code;
        $this->ticket_created_at = $this->ticket->created_at->format('Y-m-d H:i:s');
        $this->ticket_status = $this->ticket->status->getLabel();
        $this->ticket_status_color = $this->ticket->status->getColor();
        $this->ticket_stake_amount = $this->ticket->stake_amount;
        $this->ticket_won = $this->ticket->won ? 'Yes' : 'No';
        $this->ticket_prize = $this->ticket->prize;
    }

    public function render()
    {
        return view('livewire.tickets', [
            'tickets' => $this->tickets,
        ]);
    }
}
