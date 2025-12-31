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
    public $ticketDetails = [];

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
            ->paginate(10);
    }

    public function howToPlay($id)
    {
        $this->ticket = Ticket::find($id);
        return redirect()->route('tickets.howtoplay');
    }

    public function createTicket()
    {
        return redirect()->route('tickets.create');
    }

    public function viewTicket($id)
    {
        return redirect()->route('tickets.view', $id);
    }

    public function editTicket($id)
    {
        return redirect()->route('tickets.edit', $id);
    }

    public function render()
    {
        return view('livewire.tickets', [
            'tickets' => $this->tickets,
        ]);
    }
}
