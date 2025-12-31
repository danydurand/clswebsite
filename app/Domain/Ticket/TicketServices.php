<?php

namespace App\Domain\Ticket;

use App\Models\Bank;
use App\Models\Game;
use App\Models\Ticket;
use App\Classes\PResponse;
use Illuminate\Support\Str;
use App\Models\SaleSequence;
use App\Models\TicketAction;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Domain\Ticket\TicketStatusEnum;

class TicketServices
{

    public static function validateData(array $data): PResponse
    {
        info('');
        info('TicketServices::validateData');
        info('============================');
        info('');

        info('Validating: ' . print_r($data, true));

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
        info('Ok 1');
        // return $response;
        //------------------------------
        // The User must provide a bet
        //------------------------------
        if (strlen($bet) <= 1) {
            $response->userMessage = 'Type the Bet (at least to digits)';
            return $response;
        }
        info('Ok 2');
        //---------------------------------------
        // The User must provide a stake amount
        //---------------------------------------
        if (strlen($stake) === 0) {
            $response->userMessage = 'Type the Stake Amount';
            return $response;
        }
        info('Ok 3');
        //------------------------------------------------
        // The stake amount must be in the allowed range
        //------------------------------------------------
        $min = sett('minimum-bet', 'float', 0.5);
        $max = sett('maximum-bet', 'float', 600);
        if (($stake < $min) || ($stake > $max)) {
            $response->userMessage = "The Stake must be between: $min and $max";
            return $response;
        }
        info('Ok 4');
        //-----------------------
        // The Game must exists 
        //-----------------------
        $game = Game::find($data['game_id']);
        if (!($game instanceof Game)) {
            $response->userMessage = "Invalid Game";
            return $response;
        }
        info('Ok 5');
        //--------------------------
        // The bet must be numeric 
        //--------------------------
        if (!is_numeric($bet)) {
            $response->userMessage = 'The Bet must be numeric';
            return $response;
        }
        info('Ok 6');
        //------------------------------------------------------------
        // The bet number' digits count must match the selected game
        //------------------------------------------------------------
        $length = strlen($bet);
        $gamePick = $game->pick;
        if ($length != $gamePick) {
            $response->userMessage = "Invalid Bet, you must provide $gamePick digits";
            return $response;
        }
        info('Ok 7');
        //----------------------------------------------------------------------------
        // Based on the selected game, we create the required sequence which will be
        // used to obtain the bet numbers 
        //----------------------------------------------------------------------------
        $sequence = SaleSequence::where('game_id', $game->id)->get();

        // dd($sequence[0]['char']);

        info('sequence: ' . print_r($sequence, true));
        $bet .= $sequence[0]['char'];
        info('bet: ' . $bet);
        // dd($bet);
        //------------------------------------------
        // The bet must have a valid sale sequence
        //------------------------------------------
        $betParsed = self::parseBet($bet);
        $betNumbers = [$betParsed['numbers']];
        $betType = $betParsed['bet_type'];
        $pick = $betParsed['pick'];
        if ($betType === '/') {
            $betNumbers = self::marriageCombinations($betParsed['numbers']);
        }
        info('betNumbers: ' . print_r($betNumbers, true));
        info('betType: ' . $betType);
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
        info('Ok 8');
        //-------------------------------------------------------------------------------------
        // Returns the real games to be played and the bet numbers clean without any modifier
        //-------------------------------------------------------------------------------------
        $response->okay = true;
        $response->qtyErrors = 0;
        $response->setData('colorMessage', 'green');
        $response->setData('games', [['game_id' => $data['game_id']]]);
        $response->setData('validRaffles', $raffle);
        $response->setData('betNumbers', $betNumbers);
        // dd($response);
        return $response;
    }

    public static function marriageCombinations(string $digits): array
    {
        $first = $digits[0];
        $second = $digits[1];
        $third = $digits[2];
        $fourth = $digits[3];

        $games = [
            $first . $second . $third . $fourth,
            $first . $second . $fourth . $third,
            $second . $first . $third . $fourth,
            $second . $first . $fourth . $third
        ];

        $games = array_values(array_unique($games, SORT_STRING));
        return $games;
    }

    public static function allCombinations($numbers, $length)
    {
        $result = [];
        $numbersArray = str_split($numbers); // Convert the string of numbers to an array
        $uniqueNumbers = array_unique($numbersArray); // Get unique numbers to ensure all are included

        // Generate all possible combinations
        self::generateCombinations($numbersArray, $length, '', $result, $uniqueNumbers);

        return $result;
    }

    public static function generateCombinations($numbersArray, $length, $current, &$result, $uniqueNumbers)
    {
        if (strlen($current) == $length) {
            // Check if the current combination includes all unique numbers
            if (self::containsAllNumbers($current, $uniqueNumbers)) {
                $result[] = $current;
            }
            return;
        }

        foreach ($numbersArray as $number) {
            self::generateCombinations($numbersArray, $length, $current . $number, $result, $uniqueNumbers);
        }
    }

    public static function containsAllNumbers($combination, $uniqueNumbers)
    {
        foreach ($uniqueNumbers as $number) {
            if (strpos($combination, $number) === false) {
                return false;
            }
        }
        return true;
    }

    public static function copy(int $ticketId, array $raffleCodes): PResponse
    {
        $response = new PResponse();
        $response->userMess = 'New Ticket';

        $origin = Ticket::find($ticketId);
        if (!($origin instanceof Ticket)) {
            $response->okay = false;
            $response->qtyErrors = 1;
            $response->userMess = 'There is no Ticket with the Id: ' . $ticketId;
            return $response;
        }
        //---------------------------------------------
        // The ticket must belong to the current user
        //---------------------------------------------
        if ($origin->customer_id !== Auth::user()->customer_id) {
            $response->okay = false;
            $response->qtyErrors = 1;
            $response->userMess = 'You can\'t copy that ticket';
            return $response;
        }
        DB::beginTransaction();
        try {
            $destiny = Ticket::create([
                'lottery_id' => null,
                'raffle_id' => null,
                'stake_amount' => $origin->stake_amount,
                'payment_status' => PaymentStatusEnum::Pending->value,
                'status' => TicketStatusEnum::Pending->value,
                'won' => false,
                'prize_amount' => 0,
                'commission' => $origin->commission,
                'profit' => $origin->profit,
                'code' => TicketServices::generateCode(),
                'scrutiny_id' => null,
                'terminal_id' => null,
                'seller_id' => $origin->seller_id,
                'bank_id' => $origin->bank_id,
            ]);
            $details = $origin->ticketDetails()->get();
            // $qtyDetails = count($details);
            // info('The ticket: '.$origin->id.' has '.$qtyDetails.' details');
            // info('Raffle Codes: '.print_r($raffleCodes, true));
            $qtyInvalid = 0;
            foreach ($details as $detail) {
                //--------------------------------------------------------------------
                // Bet that belongs to unavailable raffle will be marked as invalid
                //--------------------------------------------------------------------
                $isValidBet = true;
                // info('Checking the raffle code: '.$detail->raffle->raffle_code);
                if (!in_array($detail->raffle->raffle_code, $raffleCodes)) {
                    $isValidBet = false;
                    $qtyInvalid++;
                    // info('The raffle code: '.$detail->raffle->raffle_code.' is not valid');
                }
                $destiny->ticketDetails()->create([
                    'raffle_id' => $detail->raffle_id,
                    'game_id' => $detail->game_id,
                    'sequence' => $detail->sequence,
                    'stake_amount' => $detail->stake_amount,
                    'won' => false,
                    'prize_amount' => 0,
                    'commission_perc' => $detail->commission_perc,
                    'commission' => $detail->commission,
                    'profit' => $detail->profit,
                    'is_valid_bet' => $isValidBet,
                ]);
            }
            DB::commit();
            $response->setData('ticketId', $destiny->id);
            if ($qtyInvalid > 0) {
                $response->userMess .= ' - ' . $qtyInvalid . ' invalid bet(s)';
            }
        } catch (\Error $e) {
            DB::rollBack();
            $response->okay = false;
            $response->userMess = $e->getMessage();
            $response->qtyErrors = 1;
        }

        return $response;
    }


    public static function parseBet(string $bet)
    {

        // Extract the numbers at the beginning of the string
        preg_match('/^\d+/', $bet, $matches);
        $numbers = $matches[0] ?? '';

        // Check if there is a special character at the end
        $hasSpecialChar = preg_match('/[\W_].*$/', $bet, $specialCharMatches);
        $betType = $hasSpecialChar ? $specialCharMatches[0] : null;

        // Return the results as an array
        return [
            'numbers' => $numbers,
            'has_bet_type' => (bool) $hasSpecialChar,
            'bet_type' => $betType,
            'pick' => strlen($numbers),
        ];
    }

    public static function markAsWinner(Collection $ticketIds, int $userId, string $scrutinyCode): PResponse
    {
        $response = new PResponse();

        try {
            $qtyMarked = Ticket::query()
                ->whereIn('id', $ticketIds)
                ->update([
                    'status' => TicketStatusEnum::Winner->value,
                ]);
            info('Marked ' . $qtyMarked . ' tickets as winner');
        } catch (\Error $e) {
            $response->okay = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Marking tickets as winner. ' . $response->userMess);
            return $response;
        }
        //-------------------------------------------------------------
        // The action must be registered in the ticket actions table
        //-------------------------------------------------------------
        $ticketActions = [];
        foreach ($ticketIds as $ticketId) {
            $ticketActions[] = [
                'ticket_id' => $ticketId,
                'action' => 'Marked as Winner',
                'executed_by' => $userId,
                'executed_at' => now(),
                'security_code' => null,
                'comments' => 'Scrutiny Code: ' . $scrutinyCode,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        try {
            TicketAction::insert($ticketActions);
        } catch (\Error $e) {
            $response->okay = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Registering the action of "change status of winner tickets". ' . $response->userMess);
            return $response;
        }

        return $response;
    }


    public static function markAsLooser(Collection $ticketIds, int $userId, string $scrutinyCode): PResponse
    {
        $response = new PResponse();

        try {
            $qtyMarked = Ticket::query()
                ->whereIn('id', $ticketIds)
                ->update([
                    'status' => TicketStatusEnum::Looser->value,
                ]);
            info('Marked ' . $qtyMarked . ' tickets as looser');
        } catch (\Error $e) {
            $response->okay = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Marking tickets as looser. ' . $response->userMess);
            return $response;
        }
        //-------------------------------------------------------------
        // The action must be registered in the ticket actions table
        //-------------------------------------------------------------
        $ticketActions = [];
        foreach ($ticketIds as $ticketId) {
            $ticketActions[] = [
                'ticket_id' => $ticketId,
                'action' => 'Marked as Looser',
                'executed_by' => $userId,
                'executed_at' => now(),
                'security_code' => null,
                'comments' => 'Scrutiny Code: ' . $scrutinyCode,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        try {
            TicketAction::insert($ticketActions);
        } catch (\Error $e) {
            $response->okay = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Registering the action of "change status of looser tickets". ' . $response->userMess);
            return $response;
        }

        return $response;
    }


    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Ticket::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            return Ticket::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Ticket::query()
                ->bySupervisor($user->id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            return Ticket::query()
                ->whereIn('bank_id', $bankIds);
        }
        return Ticket::query()->where('id', '<', -1);

    }


    public static function thisMonthSoldAmount()
    {
        return bcdiv(Ticket::thisMonth()->sum('stake_amount') / 100, 2);
    }

    public static function thisMonthPrizeAmount()
    {
        return bcdiv(Ticket::thisMonth()->winner()->sum('prize_amount') / 100, 2);
    }

    public static function thisMonthProfit()
    {
        return bcsub(TicketServices::thisMonthSoldAmount(), TicketServices::thisMonthPrizeAmount(), 2);
    }

    public static function generateCode(): string
    {
        return (string) Str::uuid();
        // return strtoupper((string) Str::uuid());
    }



    public static function totalize(Ticket $ticket): PResponse
    {
        $response = new PResponse();
        info('');
        info('TicketServices::totalize');
        info('========================');
        try {
            $sqlQuery = "call lottery.spu_totalize_ticket($ticket->id)";
            DB::statement($sqlQuery);
            $response->okay = true;
            $response->userMess = __('Ticket totalized');
        } catch (\Exception $e) {
            $response->okay = false;
            $response->userMess = 'Error totalizing the ticket: ' . $e->getMessage();
            Log::error($response->userMess);
        }

        return $response;
    }






}
