<?php

namespace App\Livewire;

use App\Models\Game;
use App\Helpers\Flash;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Lottery;
use Livewire\Component;
use App\Classes\PResponse;
use App\Models\SaleSequence;
use App\Models\TicketDetail;
use Illuminate\Support\Facades\Auth;
use App\Domain\Ticket\TicketServices;

class EditTicket extends Component
{
    public $ticket;
    public $userMessage = '';
    public $colorMessage = 'red';

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $lottery_id;
    public $raffle_id;
    public $game_id;
    public $sequence;
    public $stake_amount;
    public $ticket_id;
    public $ticket_code;
    public $ticket_created_at;
    public $ticket_stake_amount;
    public $ticket_won;
    public $ticket_prize;
    public $ticket_status;
    public $ticket_status_color;
    public $ticket_payment_status;
    public $editing = false;
    public $showDeleteConfirm = false;

    public function mount($id)
    {

        $this->ticket = Ticket::findOrFail($id);
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

    #[\Livewire\Attributes\Computed]
    public function ticketDetails()
    {
        return TicketDetail::query()
            ->byTicket($this->ticket->id)
            ->with(['raffle.lottery', 'game'])
            ->get();
    }

    public function editBet($id)
    {
        // Set editing flag FIRST, before loading any data
        // This ensures it's true when Livewire re-renders
        $this->editing = true;

        info('1.- In this point the Raffle is: ' . $this->raffle_id);
        $ticketDetail = TicketDetail::findOrFail($id);

        // Set lottery_id first so the raffles list is populated
        $this->lottery_id = $ticketDetail->raffle->lottery_id;

        // Then set the raffle_id - now it will be preserved in render()
        $this->raffle_id = $ticketDetail->raffle_id;
        info('2.- In this point the Raffle is: ' . $this->raffle_id);

        // Set other fields
        $this->game_id = $ticketDetail->game_id;
        $this->sequence = $ticketDetail->sequence;
        $this->stake_amount = $ticketDetail->stake_amount;
        info('3.- In this point the Raffle is: ' . $this->raffle_id);

        $this->deleteBet($id);

        // Reset editing flag after all values are set
        $this->editing = false;
    }

    public function updateBet($id)
    {
        $ticketDetail = TicketDetail::findOrFail($id);
        $ticketDetail->update([
            'raffle_id' => $this->raffle_id,
            'game_id' => $this->game_id,
            'sequence' => $this->sequence,
            'stake_amount' => $this->stake_amount,
        ]);
        Flash::success('Bet updated successfully');
        $this->updateTotal();
        $this->editing = false;
    }

    public function back()
    {
        return redirect()->route('tickets.index');
    }

    public function howToPlay($id)
    {
        return redirect()->route('tickets.how-to-play', $id);
    }

    public function viewTicket($id)
    {
        return redirect()->route('tickets.view', $id);
    }

    public function deleteTicket($id)
    {
        $this->showDeleteConfirm = false;
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        Flash::success('Ticket deleted successfully');
        return redirect()->route('tickets.index');
    }

    public function updateTotal()
    {
        TicketServices::totalize($this->ticket);
        $this->ticket->refresh();
        $this->ticket_stake_amount = $this->ticket->stake_amount;
    }

    public function deleteBet($id)
    {
        $ticketDetail = TicketDetail::findOrFail($id);
        $ticketDetail->delete();

        $this->updateTotal();

        if (!$this->editing) {
            Flash::success('Bet deleted successfully');
        }

        $qtyDetails = $this->ticketDetails->count();

        if ($qtyDetails === 0) {
            Flash::warning("Tickets with no bets will be auto-cancelled within 2 minutes (max)");
        }
    }

    public function updatedLotteryId($value)
    {
        if ($this->editing) {
            return;
        }
        info('I will reset the raffle_id');
        // When lottery changes, update the raffles list
        $this->raffle_id = null; // Reset raffle selection
    }

    // public function validateData(array $data): PResponse
    // {
    //     $response = new PResponse();
    //     $response->okay = false;
    //     $response->qtyErrors = 1;
    //     $response->setData('colorMessage', 'red');

    //     $bet = $data['sequence'];
    //     $stake = $data['stake_amount'];
    //     $raffle = $data['raffle_id'];
    //     //-------------------------------------------
    //     // The User must select a Raffle to play in
    //     //-------------------------------------------
    //     if (strlen($raffle) === 0) {
    //         $response->userMessage = 'Choose a Raffle';
    //         return $response;
    //     }
    //     //------------------------------
    //     // The User must provide a bet
    //     //------------------------------
    //     if (strlen($bet) <= 1) {
    //         $response->userMessage = 'Type the Bet (at least to digits)';
    //         return $response;
    //     }
    //     //---------------------------------------
    //     // The User must provide a stake amount
    //     //---------------------------------------
    //     if (strlen($stake) === 0) {
    //         $response->userMessage = 'Type the Stake Amount';
    //         return $response;
    //     }
    //     //------------------------------------------------
    //     // The stake amount must be in the allowed range
    //     //------------------------------------------------
    //     $min = sett('minimum-bet', 'float', 0.5);
    //     $max = sett('maximum-bet', 'float', 600);
    //     if (($stake < $min) || ($stake > $max)) {
    //         $response->userMessage = "The Stake must be between: $min and $max";
    //         Flash::error($response->userMessage);
    //         return $response;
    //     }
    //     //------------------------------------------
    //     // The bet must have a valid sale sequence
    //     //------------------------------------------
    //     $betParsed = TicketServices::parseBet($bet);
    //     $betNumbers = [$betParsed['numbers']];
    //     $betType = $betParsed['bet_type'];
    //     $pick = $betParsed['pick'];
    //     if ($betType === '/') {
    //         $betNumbers = TicketServices::marriageCombinations($betParsed['numbers']);
    //     }
    //     //----------------------------------------------------------
    //     // The SaleSequence indicates the real games to be played
    //     //----------------------------------------------------------
    //     $games = SaleSequence::where('pick', $pick)
    //         ->where('char', $betType)
    //         ->get();
    //     //--------------------------------------------------------
    //     // If there is no match, then is an invalid bet sequence
    //     //--------------------------------------------------------
    //     if ((count($games) === 0) || (!($games[0] instanceof SaleSequence))) {
    //         info('Invalid bet sequence: ' . $bet);
    //         $response->userMessage = 'Invalid Bet';
    //         return $response;
    //     }
    //     //-------------------------------------------------------------------------------------
    //     // Returns the real games to be played and the bet numbers clean without any modifier
    //     //-------------------------------------------------------------------------------------
    //     $response->okay = true;
    //     $response->qtyErrors = 0;
    //     $response->setData('colorMessage', 'green');
    //     $response->setData('games', $games);
    //     $response->setData('validRaffles', $raffle);
    //     $response->setData('betNumbers', $betNumbers);
    //     return $response;
    // }

    // public function addBet()
    // {
    //     $data['sequence'] = $this->sequence;
    //     $data['game_id'] = $this->game_id;
    //     $data['stake_amount'] = $this->stake_amount;
    //     $data['raffle_id'] = $this->raffle_id;

    //     $validation = $this->validateData($data);
    //     if (!$validation->okay) {
    //         Flash::error($validation->userMessage);
    //         return;
    //     }

    //     // info('Validation: ' . $validation);

    //     $betNumbers = $validation->getData('betNumbers');
    //     $game_id = $validation->getData('games')[0]['game_id'];
    //     $raffle_id = $validation->getData('validRaffles');

    //     info('Game: ' . $validation->getData('games')[0]['game_id']);
    //     info('Raffle: ' . $raffle_id);
    //     info('Bet Numbers: ' . print_r($betNumbers, true));

    //     $this->ticket = Ticket::find($this->ticket->id);
    //     info('Ticket: ' . $this->ticket);
    //     if ($this->ticket->was_cancelled) {
    //         Flash::error('Ticket Cancelled');
    //         return;
    //     }

    //     $previous = $this->ticket->ticketDetails()
    //         ->where('raffle_id', $raffle_id)
    //         ->where('game_id', $game_id)
    //         ->where('sequence', $betNumbers)
    //         ->first();
    //     if ($previous instanceof TicketDetail) {
    //         // Update the previously created record
    //         $previous->stake_amount += $this->stake_amount;
    //         $previous->save();
    //         Flash::success('Bet updated successfully');
    //     } else {
    //         //-----------------------------------------------------------------
    //         // Create a new record for each sequence (betNumbers is an array)
    //         //-----------------------------------------------------------------
    //         $qtyBets = 0;
    //         for ($x = 0; $x < count($betNumbers); $x++) {
    //             $sequence = $betNumbers[$x];
    //             $this->ticket->ticketDetails()->create([
    //                 'raffle_id' => $raffle_id,
    //                 'game_id' => $game_id,
    //                 'sequence' => $sequence,
    //                 'stake_amount' => $this->stake_amount,
    //                 'won' => false,
    //                 'prize_amount' => 0,
    //                 'is_valid_bet' => true,
    //             ]);
    //             $qtyBets++;
    //         }
    //         $this->userMessage = $qtyBets > 1
    //             ? $qtyBets . ' Bet(s) Added'
    //             : 'Bet Added';
    //         $this->colorMessage = 'green';
    //     }
    //     $this->updateTotal();
    //     $this->reset(['sequence', 'stake_amount']);
    //     Flash::success('Bet added successfully');
    // }

    public function addBet()
    {
        info('');
        info('EditTicket::addBet');
        info('==================');
        info('');

        $data['sequence'] = $this->sequence;
        $data['game_id'] = $this->game_id;
        $data['stake_amount'] = $this->stake_amount;
        $data['raffle_id'] = $this->raffle_id;

        $validation = TicketServices::validateData($data);

        if (!$validation->okay) {
            Flash::error($validation->userMessage);
            return;
        }

        // dd($validation);

        $betNumbers = $validation->getData('betNumbers');
        $game_id = $validation->getData('games')[0]['game_id'];
        $raffle_id = $validation->getData('validRaffles');

        info('Game: ' . $validation->getData('games')[0]['game_id']);
        info('Raffle: ' . $raffle_id);
        info('Bet Numbers: ' . print_r($betNumbers, true));

        $this->ticket = Ticket::find($this->ticket->id);
        if ($this->ticket->was_cancelled) {
            Flash::error('Ticket Cancelled');
            return;
        }

        $previous = $this->ticket->ticketDetails()
            ->where('raffle_id', $raffle_id)
            ->where('game_id', $game_id)
            ->where('sequence', $betNumbers)
            ->first();
        if ($previous instanceof TicketDetail) {
            // Update the previously created record
            $previous->stake_amount += $this->stake_amount;
            $previous->save();
            Flash::success('Bet updated successfully');
        } else {
            //-----------------------------------------------------------------
            // Create a new record for each sequence (betNumbers is an array)
            //-----------------------------------------------------------------
            $qtyBets = 0;
            for ($x = 0; $x < count($betNumbers); $x++) {
                $sequence = $betNumbers[$x];
                $this->ticket->ticketDetails()->create([
                    'raffle_id' => $raffle_id,
                    'game_id' => $game_id,
                    'sequence' => $sequence,
                    'stake_amount' => $this->stake_amount,
                    'won' => false,
                    'prize_amount' => 0,
                    'is_valid_bet' => true,
                ]);
                $qtyBets++;
            }
            $this->userMessage = $qtyBets > 1
                ? $qtyBets . ' Bet(s) Added'
                : 'Bet Added';
            Flash::success($this->userMessage);
        }
        $this->updateTotal();
        $this->reset(['sequence', 'stake_amount']);
    }

    public function render()
    {
        // Selecting the lotteries that has available raffles
        $lotteries = Lottery::whereHas('raffles', function ($query) {
            $query->where('is_available', true);
        })->get();

        // Only set default lottery if not already set
        if (!$this->lottery_id) {
            $this->lottery_id = $lotteries->first()->id;
        }

        // Showing all games
        $games = Game::all();

        // Selecting the raffles for the selected lotteries
        $raffles = $this->lottery_id
            ? Raffle::where('lottery_id', $this->lottery_id)
                ->where('is_available', true)
                ->get()
            : collect();

        // Only set default raffle if not already set OR if the current raffle_id is not in the raffles list
        if (!$this->raffle_id && $raffles->isNotEmpty()) {
            info('Here I am 1. Editing: ' . $this->editing);
            $this->raffle_id = $raffles->first()->id;
        } elseif ($this->raffle_id && $raffles->isNotEmpty()) {
            // Verify that the current raffle_id exists in the raffles collection
            // If not, it means the lottery was changed and we should reset to first raffle
            $raffleExists = $raffles->contains('id', $this->raffle_id);
            if (!$raffleExists && !$this->editing) {
                info('Here I am 2. Editing: ' . $this->editing);
                $this->raffle_id = $raffles->first()->id;
            }
        }
        if (!$this->game_id && $games->isNotEmpty()) {
            $this->game_id = $games->first()->id;
        }

        return view('livewire.tickets.edit-ticket', [
            'lotteries' => $lotteries,
            'raffles' => $raffles,
            'games' => $games,
            'ticketDetails' => $this->ticketDetails,
        ]);
    }
}
