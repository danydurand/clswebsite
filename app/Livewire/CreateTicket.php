<?php

namespace App\Livewire;

use Flux\Flux;
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
use App\Domain\Ticket\TicketStatusEnum;
use App\Domain\Ticket\PaymentStatusEnum;

class CreateTicket extends Component
{
    public $ticket;
    public $lottery_id;
    public $raffle_id;
    public $game_id;
    public $sequence;
    public $stake_amount;
    public $userMessage = '';
    public $colorMessage = 'red';
    // public $ticketDetails = [];
    public $sortBy = 'id';
    public $sortDirection = 'asc';

    public function mount(?int $raffle_id = null)
    {
        $this->raffle_id = $raffle_id;

        // Selecting the lotteries that has available raffles
        $lotteries = Lottery::whereHas('raffles', function ($query) {
            $query->where('is_available', true);
        })->get();

        $this->lottery_id = $lotteries->first()->id;

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
    //     //----------------------------------------------------------------
    //     // We evaluate if the raffle field contains more than one raffle
    //     //----------------------------------------------------------------
    //     // $result = $this->evaluateRaffles($raffle, $qtyRaffles);
    //     // if ($result['error']) {
    //     //     $response->userMessage = $result['message'];
    //     //     return $response;
    //     // }
    //     // $validRaffles = $result['valid_raffles'];
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
    //         return $response;
    //     }
    //     //-----------------------
    //     // The Game must exists 
    //     //-----------------------
    //     $game = Game::find($data['game_id']);
    //     if (!($game instanceof Game)) {
    //         $response->userMessage = "Invalid Game";
    //         return $response;
    //     }
    //     //--------------------------
    //     // The bet must be numeric 
    //     //--------------------------
    //     if (!is_numeric($bet)) {
    //         $response->userMessage = 'The Bet must be numeric';
    //         return $response;
    //     }
    //     //------------------------------------------------------------
    //     // The bet number' digits count must match the selected game
    //     //------------------------------------------------------------
    //     $length = strlen($bet);
    //     $gamePick = $game->pick;
    //     if ($length != $gamePick) {
    //         $response->userMessage = "Invalid Bet, you must provide $gamePick digits";
    //         return $response;
    //     }
    //     //------------------------------------------
    //     // The bet must have a valid sale sequence
    //     //------------------------------------------
    //     // $betParsed = TicketServices::parseBet($bet);
    //     // $betNumbers = [$betParsed['numbers']];
    //     // $betType = $betParsed['bet_type'];
    //     // $pick = $betParsed['pick'];
    //     // if ($betType === '/') {
    //     //     $betNumbers = TicketServices::marriageCombinations($betParsed['numbers']);
    //     // }
    //     //----------------------------------------------------------
    //     // The SaleSequence indicates the real games to be played
    //     //----------------------------------------------------------
    //     // $games = SaleSequence::where('pick', $pick)
    //     //     ->where('char', $betType)
    //     //     ->get();
    //     //--------------------------------------------------------
    //     // If there is no match, then is an invalid bet sequence
    //     //--------------------------------------------------------
    //     // if ((count($games) === 0) || (!($games[0] instanceof SaleSequence))) {
    //     //     info('Invalid bet sequence: ' . $bet);
    //     //     $response->userMessage = 'Invalid Bet';
    //     //     return $response;
    //     // }
    //     //-------------------------------------------------------------------------------------
    //     // Returns the real games to be played and the bet numbers clean without any modifier
    //     //-------------------------------------------------------------------------------------
    //     $response->okay = true;
    //     $response->qtyErrors = 0;
    //     $response->setData('colorMessage', 'green');
    //     $response->setData('games', $game->id);
    //     $response->setData('validRaffles', $raffle);
    //     $response->setData('betNumbers', $bet);
    //     return $response;
    // }


    public function save(?int $ticketId = null) //: PResponse
    {

        $data['game_id'] = $this->game_id;
        $data['sequence'] = $this->sequence;
        $data['stake_amount'] = $this->stake_amount;
        $data['raffle_id'] = $this->raffle_id;

        $validation = TicketServices::validateData($data);
        // $validation = $this->validateData($data);
        if (!$validation->okay) {
            Flash::error($validation->userMessage);
            return;
        }

        // info('Validation: ' . $validation);

        $betNumbers = $validation->getData('betNumbers');
        // $game_id = $validation->getData('games')[0]['game_id'];
        $game_id = $validation->getData('game_id');
        $raffle_id = $validation->getData('validRaffles');

        $user = Auth::user();

        // info('Game: ' . $game_id);
        // info('Raffle: ' . $raffle_id);
        // info('Bet Numbers: ' . print_r($betNumbers, true));

        if (!$ticketId) {
            //----------------------
            // Creating the Ticket
            //----------------------
            $this->ticket = Ticket::create([
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
            Flash::success($this->userMessage);
        } else {
            $this->ticket = Ticket::find($ticketId);
            info('Ticket: ' . $this->ticket);
            if ($this->ticket->was_cancelled) {
                Flash::error('Ticket Cancelled');
                return;
            }
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
            Flash::success('Bet Added');
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

        // $this->ticketDetails = $ticket->ticketDetails()->get();

        // dd('Ticket Details: ' . print_r($this->ticketDetails, true));


        $this->reset('game_id', 'sequence', 'stake_amount');

        Flux::modal('create-ticket')->close();


        // $this->dispatch('create-ticket');

        $this->redirectRoute('tickets.edit', $this->ticket->id, navigate: true);

        // $response->setData('ticketId', $ticket->id);
        // return $response;

    }


}
