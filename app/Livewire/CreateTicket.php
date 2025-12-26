<?php

namespace App\Livewire;

use Flux\Flux;
use App\Models\Game;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Lottery;
use Livewire\Component;
use App\Classes\PResponse;
use App\Models\SaleSequence;
use App\Models\TicketDetail;
use Illuminate\Support\Facades\Auth;
use App\Domain\Ticket\TicketServices;
use App\Domain\Ticket\TicketStatusEnum;
use App\Domain\Ticket\PaymentStatusEnum;

class CreateTicket extends Component
{
    public $lottery_id;
    public $raffle_id;
    public $game_id;
    public $sequence;
    public $stake_amount;
    public $userMessage = '';
    public $colorMessage = 'red';
    public $ticketDetails = [];
    public $sortBy = 'id';
    public $sortDirection = 'asc';

    public function mount()
    {
        // Selecting the lotteries that has available raffles
        $lotteries = Lottery::whereHas('raffles', function ($query) {
            $query->where('is_available', true);
        })->get();

        $this->lottery_id = $lotteries->first()->id;

    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedLotteryId($value)
    {
        // When lottery changes, update the raffles list
        $this->raffle_id = null; // Reset raffle selection
    }

    public function render()
    {

        // Showing the lotteries that has available raffles
        $lotteries = Lottery::whereHas('raffles', function ($query) {
            $query->where('is_available', true);
        })->get();

        // Showing all games
        $games = Game::all();

        // Selecting the raffles for the selected lotteries
        $raffles = $this->lottery_id
            ? Raffle::where('lottery_id', $this->lottery_id)
                ->where('is_available', true)
                ->get()
            : collect();

        // Selecting the first raffle and game
        $this->raffle_id = $raffles?->first()->id;
        $this->game_id = $games->first()->id;

        return view('livewire.create-ticket', [
            'lotteries' => $lotteries,
            'raffles' => $raffles,
            'games' => $games,
        ]);
    }


    public function validateData(array $data): PResponse
    {
        $response = new PResponse();
        $response->okay = false;
        $response->qtyErrors = 1;
        $response->setData('colorMessage', 'red');

        $bet = $data['sequence'];
        $stake = $data['stake_amount'];
        $raffle = $data['raffle_id'];
        //-------------------------------------------
        // The User must select a Raffle to play in
        //-------------------------------------------
        if (strlen($raffle) === 0) {
            $response->userMessage = 'Choose a Raffle';
            return $response;
        }
        //----------------------------------------------------------------
        // We evaluate if the raffle field contains more than one raffle
        //----------------------------------------------------------------
        // $result = $this->evaluateRaffles($raffle, $qtyRaffles);
        // if ($result['error']) {
        //     $response->userMessage = $result['message'];
        //     return $response;
        // }
        // $validRaffles = $result['valid_raffles'];
        //------------------------------
        // The User must provide a bet
        //------------------------------
        if (strlen($bet) <= 1) {
            $response->userMessage = 'Type the Bet (at least to digits)';
            return $response;
        }
        //---------------------------------------
        // The User must provide a stake amount
        //---------------------------------------
        if (strlen($stake) === 0) {
            $response->userMessage = 'Type the Stake Amount';
            return $response;
        }
        //------------------------------------------------
        // The stake amount must be in the allowed range
        //------------------------------------------------
        $min = sett('minimum-bet', 'float', 0.5);
        $max = sett('maximum-bet', 'float', 600);
        if (($stake < $min) || ($stake > $max)) {
            $response->userMessage = "The Stake must be between: $min and $max";
            return $response;
        }
        //------------------------------------------
        // The bet must have a valid sale sequence
        //------------------------------------------
        $betParsed = TicketServices::parseBet($bet);
        $betNumbers = [$betParsed['numbers']];
        $betType = $betParsed['bet_type'];
        $pick = $betParsed['pick'];
        if ($betType === '/') {
            $betNumbers = TicketServices::marriageCombinations($betParsed['numbers']);
        }
        //----------------------------------------------------------
        // The SaleSequence indicates the real games to be played
        //----------------------------------------------------------
        $games = SaleSequence::where('pick', $pick)
            ->where('char', $betType)
            ->get();
        //--------------------------------------------------------
        // If there is no match, then is an invalid bet sequence
        //--------------------------------------------------------
        if ((count($games) === 0) || (!($games[0] instanceof SaleSequence))) {
            info('Invalid bet sequence: ' . $bet);
            $response->userMessage = 'Invalid Bet';
            return $response;
        }
        //-------------------------------------------------------------------------------------
        // Returns the real games to be played and the bet numbers clean without any modifier
        //-------------------------------------------------------------------------------------
        $response->okay = true;
        $response->qtyErrors = 0;
        $response->setData('colorMessage', 'green');
        $response->setData('games', $games);
        $response->setData('validRaffles', $raffle);
        $response->setData('betNumbers', $betNumbers);
        return $response;
    }


    public function save(?int $ticketId = null) //: PResponse
    {

        $data['sequence'] = $this->sequence;
        $data['stake_amount'] = $this->stake_amount;
        $data['raffle_id'] = $this->raffle_id;

        $validation = $this->validateData($data);
        if (!$validation->okay) {
            $this->userMessage = $validation->userMessage;
            $this->colorMessage = 'red';
            return;
        }

        // info('Validation: ' . $validation);

        $betNumbers = $validation->getData('betNumbers');
        $game_id = $validation->getData('games')[0]['game_id'];
        $raffle_id = $validation->getData('validRaffles');

        $user = Auth::user();

        // info('Game: ' . $game_id);
        // info('Raffle: ' . $raffle_id);
        // info('Bet Numbers: ' . print_r($betNumbers, true));

        if (!$ticketId) {
            //----------------------
            // Creating the Ticket
            //----------------------
            $ticket = Ticket::create([
                'customer_id' => $user->customer_id,
                'stake_amount' => $this->stake_amount,
                'payment_status' => PaymentStatusEnum::Pending->value,
                'status' => TicketStatusEnum::Pending->value,
                'won' => false,
                'prize_amount' => 0,
                'commission' => 0,
                'profit' => 0,
                'code' => TicketServices::generateCode(),
                'terminal_id' => null,
            ]);
            $this->userMessage = 'Ticket Created';
            $this->colorMessage = 'green';
        } else {
            $ticket = Ticket::find($ticketId);
            info('Ticket: ' . $ticket);
            if ($ticket->was_cancelled) {
                $this->userMessage = 'Ticket Cancelled';
                $this->colorMessage = 'red';
                return;
            }
        }

        $previous = $ticket->ticketDetails()
            ->where('raffle_id', $raffle_id)
            ->where('game_id', $game_id)
            ->where('sequence', $betNumbers)
            ->first();
        if ($previous instanceof TicketDetail) {
            // Update the previously created record
            $previous->stake_amount += $this->stake_amount;
            $previous->save();
            $this->userMessage = 'Bet Updated';
            $this->colorMessage = 'green';
        } else {
            //-----------------------------------------------------------------
            // Create a new record for each sequence (betNumbers is an array)
            //-----------------------------------------------------------------
            $qtyBets = 0;
            for ($x = 0; $x < count($betNumbers); $x++) {
                $sequence = $betNumbers[$x];
                $ticket->ticketDetails()->create([
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
            $this->colorMessage = 'green';
        }

        $this->ticketDetails = $ticket->ticketDetails()->get();

        // dd('Ticket Details: ' . print_r($this->ticketDetails, true));

        /*
        $gameIds = explode(',', $games[0]['game_id']);
        $loop = count($gameIds);
        for ($i = 0; $i < count($raffleCodes); $i++) {

            $raffle = Raffle::findByCode($raffleCodes[$i]);

            $raffleId = $raffle->id;
            //-----------------------------
            // Creating the Ticket Detail
            //-----------------------------
            for ($k = 0; $k < $loop; $k++) {
                $previous = $ticket->ticketDetails()
                    ->where('raffle_id', $raffleId)
                    ->where('game_id', $gameIds[$k])
                    ->where('sequence', $betNumbers)
                    ->first();
                if ($previous instanceof TicketDetail) {
                    // Update the previously created record
                    $previous->stake_amount += $this->stake;
                    $previous->save();
                    $response->userMessage = 'Bet Updated';
                } else {
                    //-----------------------------------------------------------------
                    // Create a new record for each sequence (betNumbers is an array)
                    //-----------------------------------------------------------------
                    $qtyBets = 0;
                    for ($x = 0; $x < count($betNumbers); $x++) {
                        $sequence = $betNumbers[$x];
                        $ticket->ticketDetails()->create([
                            'raffle_id' => $raffleId,
                            'game_id' => $gameIds[$k],
                            'sequence' => $sequence,
                            'stake_amount' => $this->stake,
                            'won' => false,
                            'prize_amount' => 0,
                            'is_valid_bet' => true,
                        ]);
                        $qtyBets++;
                    }
                    $response->userMessage = $qtyBets > 1
                        ? $qtyBets . ' Bet(s) Added'
                        : 'Bet Added';
                }
            }
        }
        */

        $this->reset('game_id', 'sequence', 'stake_amount');

        // Flux::modal('create-ticket')->close();

        session()->flash('success', 'Bet added successfully');

        // $this->dispatch('create-ticket');

        // $this->redirectRoute('raffles', navigate: true);

        // $response->setData('ticketId', $ticket->id);
        // return $response;

    }


}
