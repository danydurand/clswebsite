<?php

namespace App\Livewire;

use App\Helpers\Flash;
use App\Models\Ticket;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ViewTicket extends Component
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
    public $showDeleteConfirm = false;

    public function mount($id)
    {
        $this->ticket = Ticket::findOrFail($id);
        $this->ticketDetails = $this->ticket->ticketDetails()->with(['raffle.lottery', 'game'])->get();
        $this->ticket_id = $this->ticket->id;
        $this->ticket_code = $this->ticket->code;
        $this->ticket_created_at = $this->ticket->created_at->format('Y-m-d H:i:s');
        $this->ticket_stake_amount = $this->ticket->stake_amount;
        $this->ticket_won = $this->ticket->won ? 'Won' : '--';
        $this->ticket_prize = $this->ticket->prize;
        $this->ticket_status = $this->ticket->status->getLabel();
        $this->ticket_status_color = $this->ticket->status->getColor();
        $this->ticket_payment_status = $this->ticket->payment_status->getLabel();
    }

    public function back()
    {
        return redirect()->route('tickets.index');
    }

    public function editTicket($id)
    {
        return redirect()->route('tickets.edit', $id);
    }

    public function deleteTicket($id)
    {
        $this->showDeleteConfirm = false;
        $ticket = Ticket::findOrFail($id);
        $ticket->actions()->delete();
        $ticket->delete();
        Flash::success('Ticket deleted successfully');
        return redirect()->route('tickets.index');
    }

    public function printTicket()
    {
        // Register the print action in the database
        // $this->ticket->actions()->create([
        //     'ticket_id' => $this->ticket->id,
        //     'action' => \App\Domain\Ticket\TicketActionEnum::Printed,
        //     'executed_by' => Auth::user()->id,
        //     'executed_at' => now(),
        //     'security_code' => $this->ticket->security_code,
        //     'comments' => 'Ticket printed from online view',
        // ]);

        // Redirect to printable view
        return redirect()->route('tickets.print', $this->ticket->id);
    }

    public function sendEmail()
    {
        Flash::success('Ticket sent successfully to ' . $this->ticket->customer->email);

        // try {
        //     // Send email with PDF attachment
        //     \Illuminate\Support\Facades\Mail::to($this->ticket->customer->email)
        //         ->send(new \App\Mail\TicketEmail($this->ticket));

        //     // Register the email sent action in the database
        //     // $this->ticket->actions()->create([
        //     //     'ticket_id' => $this->ticket->id,
        //     //     'action' => \App\Domain\Ticket\TicketActionEnum::EmailSent,
        //     //     'executed_by' => auth()->id(),
        //     //     'executed_at' => now(),
        //     //     'security_code' => $this->ticket->security_code,
        //     //     'comments' => 'Ticket sent via email to customer',
        //     // ]);

        //     Flash::success('Ticket sent successfully to ' . $this->ticket->customer->email);
        // } catch (\Exception $e) {
        //     Flash::error('Failed to send email: ' . $e->getMessage());
        // }
    }

    public function sendWhatsApp()
    {
        // Register the WhatsApp sent action in the database
        // $this->ticket->actions()->create([
        //     'ticket_id' => $this->ticket->id,
        //     'action' => \App\Domain\Ticket\TicketActionEnum::WhatsAppSent,
        //     'executed_by' => auth()->id(),
        //     'executed_at' => now(),
        //     'security_code' => $this->ticket->security_code,
        //     'comments' => 'Ticket sent via WhatsApp to customer',
        // ]);

        // Generate WhatsApp message with ticket details
        $message = "🎫 *Your Lottery Ticket*\n\n";
        $message .= "Ticket ID: #{$this->ticket->id}\n";
        $message .= "Code: {$this->ticket->code}\n";
        $message .= "Total Amount: $" . number_format($this->ticket->stake_amount, 2) . "\n\n";
        $message .= "View your ticket: " . route('tickets.print', $this->ticket->id);

        // Create WhatsApp link
        $phone = preg_replace('/[^0-9]/', '', $this->ticket->customer->phone);
        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        Flash::success('Opening WhatsApp to send ticket to ' . $this->ticket->customer->phone);

        // Redirect to WhatsApp
        return redirect()->away($whatsappUrl);
    }

    public function render()
    {
        return view('livewire.tickets.view-ticket');
    }
}
