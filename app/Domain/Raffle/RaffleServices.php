<?php

namespace App\Domain\Raffle;

use App\Models\Game;
use App\Models\User;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Scrutiny;
use App\Classes\PResponse;
use App\Models\Consortium;
use Illuminate\Support\Str;
use App\Models\TicketDetail;
use App\Models\PaymentDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ResultWinnerSequence;
use Illuminate\Support\Facades\Auth;
use App\Domain\Ticket\TicketServices;
use App\Domain\Ticket\TicketStatusEnum;
use App\Domain\Scrutiny\ScrutinyServices;

class RaffleServices
{

    public static function makingTodayRafflesUnavailable(): PResponse
    {
        $response = new PResponse();
        $currentDate = now()->toDateString();
        $currentTime = now()->toTimeString();

        try {
            //------------------------------------------------------
            // Marking all raffles as not available for other days
            //------------------------------------------------------
            $qty = Raffle::where('raffle_date', $currentDate)
                ->where('stop_sale_time', '<=', $currentTime)
                ->where('is_available', true)
                ->update(['is_available' => false]);
            info($qty . ' Raffles marked as unavailable for today');
            $response->userMess = $qty . ' Raffles marked as unavailable for today';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }

    public static function makeAvailableTodayRaffles(): PResponse
    {
        $response = new PResponse();
        try {
            //------------------------------------------------------
            // Marking all raffles as not available for other days
            //------------------------------------------------------
            Raffle::where('raffle_date', '!=', now()->toDateString())
                ->update(['is_available' => false]);
            //------------------------------------------
            // Making available all raffles for today
            //------------------------------------------
            $qty = Raffle::where('raffle_date', now()->toDateString())
                ->whereHas('lottery', function ($query) {
                    $query->where('is_active', true);
                })
                ->update(['is_available' => true]);
            info($qty . ' Raffles made available for today');
            $response->userMess = $qty . ' Raffles made available for today';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }


    public static function generateRaffleCode(string $lottCode, string $time): string
    {
        return $lottCode . '-' . trim($time);
    }

    public static function generateCode(string $lottCode, string $date, string $time): string
    {
        return $lottCode . '-' . $date . '-' . substr(trim($time), 0, 2);
    }

    public static function doScrutinyOnRaffles(array $raffles): PResponse
    {
        $response = new PResponse();
        foreach ($raffles as $raffle) {
            $iResponse = self::doScrutiny($raffle);
            if (!$iResponse->indiOkey) {
                Log::error($iResponse->userMess);
                $response->qtyErrors++;
            } else {
                $response->qtyProcessed++;
            }
        }
        $response->userMess = $response->qtyProcessed . ' Raffles Scrutinized';
        Log::info($response->userMess);
        return $response;
    }

    public static function doScrutiny(Raffle $raffle): PResponse
    {
        info('');
        info('RaffleServices::doScrutiny');
        info('==========================');

        $response = new PResponse();
        $raffleId = $raffle->id;
        $result = $raffle->result;

        if (empty($result)) {
            $response->indiOkey = false;
            $response->userMess = 'Raffle without result';
            return $response;
        }
        $user = User::where('email', 'batch@dreambet.ht')->first();
        //------------------------------------------------------------
        // Getting the Result Winner Sequence for the current Raffle
        //------------------------------------------------------------
        $resultWinnerSequences = $raffle->resultWinnerSequences;

        $consortiums = Consortium::whereHas('tickets')->get();
        $qty = 0;
        foreach ($consortiums as $consortium) {
            DB::beginTransaction();
            info('' . $consortium->name);
            info('Processing Consortium: ' . $consortium->name);
            //------------------------------------------------
            // Checking the previous existence of a scrutiny
            //------------------------------------------------
            $scrutiny = Scrutiny::findByConsortiumAndRaffle($consortium->id, $raffleId);
            if (!$scrutiny) {
                //------------------------------
                // Creating a new scrutiny
                //------------------------------
                // info('Creating a new scrutiny record');
                try {
                    $scrutiny = Scrutiny::create([
                        'consortium_id' => $consortium->id,
                        'lottery_id' => $raffle->lottery_id,
                        'raffle_id' => $raffleId,
                        'code' => ScrutinyServices::generateCode($consortium->id),
                        'qty_winners' => 0,
                        'total_bet_amount' => 0,
                        'total_prize_amount' => 0,
                    ]);
                } catch (\Error $e) {
                    $response->indiOkey = false;
                    $response->userMess = 'Error: ' . $e->getMessage();
                    Log::error('Creating the Scrutiny. ' . $response->userMess);
                    DB::rollback();
                    return $response;
                }
            } else {
                //------------------------------
                // The scrutiny already exists
                //------------------------------
                // info('The scrutiny already exists. Reseting the related ticket detials');
                try {
                    TicketDetail::where('raffle_id', $raffleId)
                        ->update([
                            'won' => false,
                            'prize_amount' => 0,
                        ]);
                    info('Reseting the Scrutiny ifself');
                    $scrutiny->update([
                        'qty_winners' => 0,
                        'total_bet_amount' => 0,
                        'total_prize_amount' => 0,
                    ]);
                } catch (\Error $e) {
                    $response->indiOkey = false;
                    $response->userMess = 'Error: ' . $e->getMessage();
                    Log::error('Reseting the Ticket Details and Scrutiny. ' . $response->userMess);
                    DB::rollback();
                    return $response;
                }
            }
            //-----------------------------------------------------------
            // Selecting the bets of the current consortium and raffle
            //-----------------------------------------------------------
            info('Selecting the bets of the current consortium and raffle');
            $bets = TicketDetail::with('ticket')
                ->whereHas('ticket', function ($query) use ($consortium) {
                    $query->whereNotIn('status', [
                        TicketStatusEnum::Cancelled->value,
                        TicketStatusEnum::AutoCancelled->value,
                    ]);
                    $query->where('consortium_id', $consortium->id);
                })
                ->where('raffle_id', $raffleId)
                ->where('won', false)
                ->get();
            info('Found ' . $bets->count() . ' bets');
            foreach ($bets as $bet) {
                //----------------------
                // Checking winner bets
                //----------------------
                info('Checking bet: ' . $bet->id . ' Game: ' . $bet->game->name . ' Sequence: ' . $bet->sequence);
                $exists = $resultWinnerSequences
                    ->where('sequence', $bet->sequence)
                    ->where('game_id', $bet->game_id)
                    ->first();
                if ($exists) {
                    info('Found a winner. Getting the winning factor');

                    $paymentProfile = $bet->ticket->paymentProfile;
                    $paymentDetails = $paymentProfile->details;

                    // foreach ($paymentDetails as $detail) {
                    //     if ($detail->game_id == 4) {
                    //         info('Det: '.$detail);
                    //     }
                    // }

                    $winnigFactor = $paymentDetails->where('payment_id', $bet->ticket->payment_id)
                        ->where('game_id', $bet->game_id)
                        ->where('winner_position', $exists->position_order)
                        ->first()?->winning_factor;
                    $winnigFactor = $winnigFactor ? $winnigFactor : 1;
                    info('Winning factor: ' . $winnigFactor . ' Stake Amount: ' . $bet->stake_amount);
                    try {
                        $bet->update([
                            'won' => true,
                            'prize_amount' => $bet->stake_amount * $winnigFactor,
                            'winning_factor' => $winnigFactor,
                            'scrutiny_id' => $scrutiny->id,
                        ]);
                    } catch (\Error $e) {
                        $response->indiOkey = false;
                        $response->userMess = 'Error: ' . $e->getMessage();
                        Log::error('Updating the bet as a Winner. ' . $response->userMess);
                        DB::rollback();
                        return $response;
                    }
                } else {
                    info('Not a winner');
                    try {
                        $bet->update([
                            'prize_amount' => 0,
                            'scrutiny_id' => $scrutiny->id,
                        ]);
                    } catch (\Error $e) {
                        $response->indiOkey = false;
                        $response->userMess = 'Error: ' . $e->getMessage();
                        Log::error('Updating the bet as a Looser. ' . $response->userMess);
                        DB::rollback();
                        return $response;
                    }
                }
            }
            //------------------------
            // Updating the scrutiny
            //------------------------
            info('Updating the scrutiny totals');
            try {
                $scrutiny->update([
                    'qty_winners' => $scrutiny->qty_winners + $bets->where('won', true)->count(),
                    'total_bet_amount' => $scrutiny->total_bet_amount + $bets->sum('stake_amount'),
                    'total_prize_amount' => $scrutiny->total_prize_amount + $bets->where('won', true)->sum('prize_amount'),
                ]);
            } catch (\Error $e) {
                $response->indiOkey = false;
                $response->userMess = 'Error: ' . $e->getMessage();
                Log::error('Updating the Scrutiny. ' . $response->userMess);
                DB::rollback();
                return $response;
            }
            //-------------------------------------------------------------------------------------------
            // If all ticket details have been processed, (it means: all tickets have been scrutinized)
            // then we can proceed to mark the tickets as winner when at least one of their details
            // have been marked as winner
            //-------------------------------------------------------------------------------------------
            info('Marking the tickets as winner');
            $winnerIds = Ticket::query()
                ->whereHas('ticketDetails', function ($query) use ($raffleId) {
                    $query->where('raffle_id', $raffleId)
                        ->where('won', true);
                })
                ->whereDoesntHave('ticketDetails', function ($query) use ($raffleId) {
                    $query->where('raffle_id', $raffleId)
                        ->whereNull('scrutiny_id');
                })
                ->pluck('id');
            info('Found ' . $winnerIds->count() . ' winners');
            if ($winnerIds->count() > 0) {
                $iResponse = TicketServices::markAsWinner($winnerIds, $user->id, $scrutiny->code);
                if (!$iResponse->indiOkey) {
                    $response->indiOkey = false;
                    $response->userMess = $iResponse->userMess;
                    DB::rollback();
                    return $response;
                }
            }

            info('Marking the tickets as looser');
            $looserIds = Ticket::query()
                ->whereDoesntHave('ticketDetails', function ($query) {
                    $query->where('won', true)
                        ->orWhereNull('scrutiny_id');
                })
                ->whereHas('ticketDetails') // Ensure ticket has at least one detail
                ->pluck('id');
            info('Found ' . $looserIds->count() . ' loosers');
            if ($looserIds->count() > 0) {
                $iResponse = TicketServices::markAsLooser($looserIds, $user->id, $scrutiny->code);
                if (!$iResponse->indiOkey) {
                    $response->indiOkey = false;
                    $response->userMess = $iResponse->userMess;
                    DB::rollback();
                    return $response;
                }
            }
            DB::commit();
            $qty++;
        }
        $response->qtyProcessed = $qty;
        $response->userMess = 'Scrutiny completed. ' . $qty . ' Consortiums processed';
        $response->userMess = 'Scrutiny Done !!';
        return $response;
    }



    // public static function doScrutiny(Raffle $raffle): PResponse
    // {
    //     $response = new PResponse();
    //     $result   = $raffle->result;
    //     if (empty($result)) {
    //         $response->indiOkey = false;
    //         $response->userMess = __('Raffle without result');
    //         return $response;
    //     }

    //     info('');
    //     info('RaffleServices::doScrutiny');
    //     info('==========================');
    //     try {
    //         $sqlQuery = "call lottery.spu_scrutiny($raffle->id)";
    //         DB::statement($sqlQuery);
    //         $response->indiOkey = true;
    //         $response->userMess = __('Scrutiny Done !!');
    //     } catch (\Exception $e) {
    //         $response->indiOkey = false;
    //         $response->userMess = __('Error doing Scrutiny: ') . $e->getMessage();
    //         Log::error($response->userMess);
    //     }
    //     return $response;
    // }

    public static function generateCombinations($array, $prefix, &$result)
    {
        if (count($array) == 0) {
            $result[] = $prefix;
            return;
        }

        for ($i = 0; $i < count($array); $i++) {
            // Create a new array without the current element
            $newArray = $array;
            unset($newArray[$i]);
            // Recursively generate combinations
            self::generateCombinations(array_values($newArray), $prefix . $array[$i], $result);
        }
    }


    public static function getCombinations($numbers): Collection
    {
        // Convert the string into an array of characters
        $numArray = str_split($numbers);
        $result = [];

        // Helper function to generate combinations
        // Start generating combinations
        self::generateCombinations($numArray, '', $result);

        return collect(array_unique($result));
    }


    /**
     * This routine returns the a sub-string of the given sequence that represent a
     * winner sequence for a game
     *
     * @param string $sequence Lottery result
     * @param string $winnerPositions Winner Positions of a game
     * @return string
     */
    public static function getSequence(string $sequence, string $winnerPositions): string
    {
        $pairs = preg_split('/\],\s*\[/', trim($winnerPositions, '[]'));

        $result = '';
        foreach ($pairs as $pair) {
            list($start, $length) = array_map('intval', explode(',', $pair));
            $result .= substr($sequence, $start - 1, $length);
        }

        return $result;
    }


    /**
     * This routine generates the possible winner sequences based on
     * each game winner's sequences and the given raffle' result.
     *
     * This routine must be invoked once we got a raffle result.  The main
     * idea behind this is to register in the database all possible winner
     * sequences to make it easy the Scrutiny process.
     *
     * @param \App\Models\Raffle $raffle
     * @return void
     */
    public static function generateResutWinnerSequences(Raffle $raffle): PResponse
    {
        $response = new PResponse();
        $result = $raffle->result;
        if (strlen($result) == 0) {
            $response->indiOkey = false;
            $response->userMess = __('Raffle without Result');
            return $response;
        }
        //--------------------------------------------------------------------
        // First of all, we delete every record related to the raffle in the
        // result_winner_sequences
        //--------------------------------------------------------------------
        $raffle->resultWinnerSequences()->delete();
        //-----------------------------------------------------------------------------
        // To Do: Instead of processing each game, it is better to consider only
        // the played game for the given raffle
        //-----------------------------------------------------------------------------
        $winners = [];
        $games = Game::active()->get();
        $games->each(function ($game) use ($raffle, &$winners) {
            //--------------------------------------------------------------------
            // Each game defines the possible winner sequences and we use them to
            // generate the winner sequences based on the raffle result
            //--------------------------------------------------------------------
            $gameWPS = $game->gameWinnerSequences()->get();
            $gameWPS->each(function ($wp) use ($game, $raffle, &$winners) {
                if (Str::startsWith($wp->winner_position, 'box')) {
                    //-----------------------------------------------------------------
                    // When the game is 'box' it means that the possible winner
                    // sequences are actually any combination of the numbers
                    //-----------------------------------------------------------------
                    $withoutBox = str_replace('box', '', $wp->winner_position);
                    $sequence = self::getSequence($raffle->result, $withoutBox);
                    $combinations = self::getCombinations($sequence);
                    $combinations->each(function ($permute) use ($game, $raffle, &$winners, $wp) {
                        $winners[] = [
                            'raffle_id' => $raffle->id,
                            'game_id' => $game->id,
                            'sequence' => $permute,
                            'winning_factor' => $wp->winning_factor * 100,
                            'position_order' => $wp->position_order,
                            'created_at' => now(),
                            'data' => '{}',
                        ];
                    });
                } else {
                    //------------------------------------------------------------------
                    // If the winner sequence is just a string that contains some
                    // sub-strings of the raffle result, we set the final string here.
                    //------------------------------------------------------------------
                    $sequence = self::getSequence($raffle->result, $wp->winner_position);
                    $winners[] = [
                        'raffle_id' => $raffle->id,
                        'game_id' => $game->id,
                        'sequence' => $sequence,
                        'winning_factor' => $wp->winning_factor * 100,
                        'position_order' => $wp->position_order,
                        'created_at' => now(),
                        'data' => '{}',
                    ];
                }
            });
        });
        //------------------------------------------------------------
        // Finally we store the sequences in the corresponding table
        //------------------------------------------------------------
        try {
            ResultWinnerSequence::insert($winners);
            $response->indiOkey = true;
            $response->userMess = count($winners) . ' ' . __('Winner Sequences Generated');
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->userMess = $e->getMessage();
        }

        return $response;
    }


}
