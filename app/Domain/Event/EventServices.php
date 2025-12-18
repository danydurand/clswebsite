<?php

namespace App\Domain\Event;

use App\Models\Bet;
use App\Models\Check;
use App\Models\Event;
use App\Models\Option;
use App\Models\Question;
use App\Classes\PResponse;
use App\Models\StatusCode;
use App\Models\Participant;
use App\Models\PresetOption;
use App\Models\PresetQuestion;
use Illuminate\Support\Carbon;
use App\Domain\Bet\BetTypeEnum;
use App\Domain\Bet\BetStatusEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Domain\Check\CheckServices;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialTransaction;
use App\Domain\FinancialTransaction\TrxTypeEnum;

class EventServices
{

    public static function lock(PResponse $response, $events): PResponse
    {
        if (!is_array($events) && !$events instanceof Collection) {
            $events = [$events];
        }

        info('');
        info('Locking: ' . count($events) . ' events');
        info('');

        // Locking events means locking the options for betting
        $eventIds = collect($events)
            ->pluck('id')
            ->toArray();

        $questionIds = Question::whereIn('event_id', $eventIds)
            ->pluck('id')->toArray();

        DB::beginTransaction();

        try {

            $qtyEvents = Event::unlocked()->whereIn('id', $eventIds)
                ->update(['is_locked' => true]);

            $qtyLockedOptions = Option::whereIn('question_id', $questionIds)
                ->update([
                    'is_active' => false,
                    'is_locked' => true
                ]);
            $response->qtyProcessed = $qtyLockedOptions;
            $response->userMess = "$qtyEvents Events, $qtyLockedOptions Options locked";

            DB::commit();

        } catch (\Exception $e) {

            Log::error($e->getMessage());
            $response->qtyErrors = 1;
            $response->errors[] = [
                'reference' => 'N/A',
                'message' => 'Locking Events and Options',
                'comment' => $e->getMessage(),
            ];

            DB::rollBack();
        }

        return $response;
    }



    public static function updateCustomerBalance(PResponse $response, $bets): PResponse
    {

        if (!is_array($bets)) {
            $bets = [$bets];
        }

        foreach ($bets as $bet) {

            info('');
            info('Updating Customers Balance: ' . $bet->customer->name);
            info('');

            try {
                // Create a financial transaction record
                FinancialTransaction::create([
                    'customer_id' => $bet->customer_id,
                    'amount' => $bet->return_amount * 100,
                    'charge' => 0,
                    'trx_type' => TrxTypeEnum::Prize->value,
                    'trx' => 'BET-' . $bet->id,
                    'remark' => 'Win Bet ID: ' . $bet->id,
                    'detail' => 'Return amount for Bet ID: ' . $bet->id,
                    'post_balance' => $bet->customer->balance + $bet->return_amount,
                ]);
                // Update customer's balance
                $bet->customer->balance += $bet->return_amount;
                $bet->customer->save();
            } catch (\Exception $e) {
                Log::error('Updating Customer Balance with Bet Id: ' . $bet->id . ': ' . $e->getMessage());
                $response->qtyErrors++;
                $response->errors[] = [
                    'reference' => 'Bet Id: ' . $bet->id,
                    'message' => 'Updating Customer Balance of: ' . $bet->customer->name,
                    'comment' => $e->getMessage(),
                ];
            }

        }

        return $response;
    }


    public static function calculateReturnAmount(PResponse $response, $events, Check $check): PResponse
    {
        if (!is_array($events) && !$events instanceof Collection) {
            $events = [$events];
        }

        foreach ($events as $event) {

            $qtyBets = 0;
            $qtyWinners = 0;
            $qtyLosers = 0;
            $stakeAmount = 0;
            $returnAmount = 0;

            info('');
            info('Calculating return amount for event: ' . $event->slug);
            info('');

            // Get all bets related to the event that are pending
            $bets = Bet::where('status', BetStatusEnum::Pending->value)
                ->whereHas('betDetails.question', function ($query) use ($event) {
                    $query->where('event_id', $event->id);
                })
                ->with('betDetails.option')
                ->get();

            info('Found ' . $bets->count() . ' pending bets for this event.');

            foreach ($bets as $bet) {
                try {
                    $bet->return_amount = 0;
                    $isWinner = false;

                    //---------------------
                    // Process Single Bet
                    //---------------------
                    if ($bet->type === BetTypeEnum::Single) {
                        $betDetail = $bet->betDetails->first();
                        $betDetail->status = BetStatusEnum::Lose->value;
                        if ($betDetail && $betDetail->option && $betDetail->option->is_winner) {
                            // For simple bet, return amount is stake * odds
                            $bet->return_amount = $bet->stake_amount * $betDetail->fractional_odds;
                            $betDetail->status = BetStatusEnum::Win->value;
                            $isWinner = true;
                        }
                        $betDetail->save();
                    }
                    //--------------------
                    // Process Multi Bet
                    //--------------------
                    elseif ($bet->type === BetTypeEnum::Multi) {
                        $allOptionsWinner = true;
                        $totalOdds = 1;

                        if ($bet->betDetails->isEmpty()) {
                            $allOptionsWinner = false;
                        }

                        foreach ($bet->betDetails as $betDetail) {
                            $betDetail->status = BetStatusEnum::Lose->value;
                            if (!$betDetail->option || !$betDetail->option->is_winner) {
                                $allOptionsWinner = false;
                                break;
                            }
                            // For Multi bet, we multiply the odds
                            $totalOdds *= $betDetail->fractional_odds;
                            $betDetail->status = BetStatusEnum::Win->value;
                            $betDetail->save();
                        }

                        if ($allOptionsWinner) {
                            // The final return amount is the stake * total odds
                            $bet->return_amount = $bet->stake_amount * $totalOdds;
                            $isWinner = true;
                        }
                    }
                    //--------------------
                    // Update bet status
                    //--------------------
                    $bet->won = $isWinner;
                    $bet->status = $isWinner ? BetStatusEnum::Win->value : BetStatusEnum::Lose->value;
                    $bet->result_time = now();
                    $bet->save();

                    info('Bet ID: ' . $bet->id . ' processed. Status: ' . $bet->status->value . ' Return Amount: ' . $bet->return_amount);
                    //----------------------------------------------------
                    // Update customer's balance if the bet is a winner
                    //----------------------------------------------------
                    if ($isWinner && $bet->return_amount > 0 && $bet->customer_id !== null) {
                        $returnAmount += $bet->return_amount;
                        $response = self::updateCustomerBalance($response, $bet);
                    }

                    if ($isWinner) {
                        $qtyWinners++;
                    } else {
                        $qtyLosers++;
                    }
                    $stakeAmount += $bet->stake_amount;

                    $qtyBets++;

                } catch (\Exception $e) {
                    Log::error('Error processing bet id: ' . $bet->id . ': ' . $e->getMessage());
                    $response->qtyErrors++;
                    $response->errors[] = [
                        'reference' => 'Bet Id: ' . $bet->id,
                        'message' => 'Error calculating return amount',
                        'comment' => $e->getMessage(),
                    ];
                }
            }

            // info('Check Id: '.$check->id);
            // info('Bets: '.$qtyBets);
            // info('Winners: '.$qtyWinners);
            // info('Losers: '.$qtyLosers);
            // info('Stake: '.$stakeAmount);
            // info('Return: '.$returnAmount);
            // info('Profit: '.$stakeAmount - $returnAmount);

            $event->is_locked = true;
            $event->check_id = $check->id;
            $event->qty_bets = $qtyBets;
            $event->qty_winners = $qtyWinners;
            $event->qty_losers = $qtyLosers;
            $event->total_stake_amount = $stakeAmount;
            $event->total_return_amount = $returnAmount;
            $event->profit = $stakeAmount - $returnAmount;
            $event->save();

            info('Finished calculating return amounts for event: ' . $event->slug);
            $response->qtyProcessed++;

        }

        CheckServices::updateTotals($response, $check);

        $response->userMess = $response->qtyProcessed . " Events Processed";

        return $response;
    }


    public static function revertReturnAmount(PResponse $response, $events): PResponse
    {

        if (!is_array($events)) {
            $events = [$events];
        }

        $checks = collect([]);
        foreach ($events as $event) {

            $qtyBets = 0;

            info('');
            info('Reverting return amount for event: ' . $event->slug);
            info('');

            // Get all bets related to the event that are not pending
            $bets = Bet::where('status', '!=', BetStatusEnum::Pending->value)
                ->whereHas('betDetails.question', function ($query) use ($event) {
                    $query->where('event_id', $event->id);
                })
                ->with('betDetails.option')
                ->get();

            info('Found ' . $bets->count() . ' non-pending bets for this event.');

            foreach ($bets as $bet) {
                try {
                    // Revert customer's balance if the bet was a winner
                    if ($bet->status->value === BetStatusEnum::Win->value && $bet->return_amount > 0) {
                        // Find and delete the financial transaction
                        $transaction = FinancialTransaction::where('trx', 'BET-' . $bet->id)->first();
                        if ($transaction) {
                            $bet->customer->balance -= $bet->return_amount;
                            $bet->customer->save();
                            $transaction->delete();
                        }
                    }

                    $bet->return_amount = 0;
                    $bet->status = BetStatusEnum::Pending->value;
                    $bet->result_time = null;
                    $bet->save();

                    foreach ($bet->betDetails as $betDetail) {
                        $betDetail->status = BetStatusEnum::Pending->value;
                        $betDetail->save();
                    }

                    info('Bet ID: ' . $bet->id . ' reverted. Status: ' . $bet->status->value);

                } catch (\Exception $e) {
                    Log::error('Error reverting bet id' . $bet->id . ': ' . $e->getMessage());
                    $response->qtyErrors++;
                    $response->errors[] = [
                        'reference' => 'Bet Id: ' . $bet->id,
                        'message' => 'Error reverting return amount',
                        'comment' => $e->getMessage(),
                    ];
                }
                $qtyBets++;
            }

            $response->qtyProcessed++;

            $checks->add($event->check);

            $event->check_id = null;
            $event->save();


            info('Finished reverting return amounts for event: ' . $event->slug);

        }

        CheckServices::updateTotals($response, $checks);

        $response->userMess = $response->qtyProcessed . " Events Processed.";
        return $response;
    }


    public static function setFinalState(PResponse $response, $events): PResponse
    {

        if (!is_array($events)) {
            $events = [$events];
        }

        $finishedStatusCodes = StatusCode::meansFinished()->pluck('id')->toArray();


        foreach ($events as $event) {

            info("");
            info('Setting final state for event: ' . $event->slug);
            info("");

            if (in_array($event->status_code_id, $finishedStatusCodes)) {

                $finalHomeScore = $event->final_home_score;
                $finalAwayScore = $event->final_away_score;
                $homeWins = false;
                $awayWins = false;
                $wasDraw = false;
                if ($finalHomeScore > $finalAwayScore) {
                    $homeWins = true;
                } else {
                    if ($finalHomeScore < $finalAwayScore) {
                        $awayWins = true;
                    } else {
                        $wasDraw = true;
                    }
                }
                //-------------------------------------------------------
                // Set questions to locked and mark the winning options
                //-------------------------------------------------------
                foreach ($event->questions as $question) {
                    $question->is_locked = true;
                    $question->is_checked = true;
                    $options = $question->options;

                    foreach ($options as $option) {
                        // info('Option: '.$option->name);
                        if ($option->name == 'HOME WIN' && $homeWins) {
                            $option->is_winner = true;
                            $question->win_option_id = $option->id;
                            // info('    Option: '.$option->name.' | Winner: YES');
                        }
                        if ($option->name == 'AWAY WIN' && $awayWins) {
                            $option->is_winner = true;
                            $question->win_option_id = $option->id;
                            // info('    Option: '.$option->name.' | Winner: YES');
                        }
                        if ($option->name == 'DRAW' && $wasDraw) {
                            $option->is_winner = true;
                            $question->win_option_id = $option->id;
                            // info('    Option: '.$option->name.' | Winner: YES');
                        }
                        $option->save();
                        info('    Option: ' . $option->name . ' | Winner: ' . ($option->is_winner ? 'YES' : 'NO'));
                    }
                    $question->save();
                    info('Question: ' . $question->title . ' locked with winner option ID: ' . $question->win_option_id);
                    info("");
                }
                $event->is_locked = true;
                $event->save();
            }
            info("");
            info('Final state set for event: ' . $event->slug);
            info("");

        }

        return $response;
    }



    public static function createInitialQuestions(Event $event)
    {
        $response = new PResponse();

        //-------------------------------------------------------------------------
        // Based on the category of the game, here we assign the initial question
        // taking them from the Preset Questions
        //-------------------------------------------------------------------------
        $specificQuestions = PresetQuestion::active()
            ->byCategory($event->category_id)
            ->get()
            ->toArray();
        $generalQuestions = PresetQuestion::active()
            ->allCategories()
            ->except($event->category_id)
            ->get()
            ->toArray();
        $presetQuestions = array_merge($specificQuestions, $generalQuestions);
        $questions = [];
        $options = [];
        foreach ($presetQuestions as $presetQuestion) {
            $questions[] = [
                'event_id' => $event->id,
                'title' => $presetQuestion['title'],
                'is_active' => true,
                'is_locked' => false,
                'preset_question_id' => $presetQuestion['id'],
            ];
        }

        try {
            $qtyQuestions = Question::insert($questions);
            $userMess = $qtyQuestions . ' Initial questions created';
            $response->userMess = $userMess;
            //----------------------------------------------------------
            // Now that we have created the initial questions, we will
            // create the corresponding options too
            //----------------------------------------------------------
            foreach ($event->questions as $question) {
                $pOptions = PresetOption::active()
                    ->byQuestion($question->preset_question_id)
                    ->get();
                foreach ($pOptions as $option) {
                    $options[] = [
                        'question_id' => $question->id,
                        'name' => $option->option,
                        'odds' => null,
                        'is_active' => true,
                        'is_locked' => false,
                        'is_winner' => false,
                        'preset_option_id' => $option->id
                    ];
                }
            }
            $qtyOptions = Option::insert($options);
            if ($qtyOptions > 0) {
                $userMess = ' | ' . $qtyOptions . ' Initial options created';
                $response->userMess .= $userMess;
            }
        } catch (\Error $e) {
            $response->qtyErrors = 1;
            $response->userMess = $e->getMessage();
        }

        return $response;
    }

    public static function generateSlug(?int $homeParticipantId, ?int $awayParticipantId, ?string $startTime): string
    {

        $slug = '';
        if ($homeParticipantId) {
            $homeParticipant = Participant::find($homeParticipantId);
            if ($homeParticipant) {
                $slug = $homeParticipant->short_name;
            }
        }
        if ($awayParticipantId) {
            $awayParticipant = Participant::find($awayParticipantId);
            if ($awayParticipant) {
                $slug = $slug . '-' . $awayParticipant->short_name;
            }
        }
        if ($startTime) {
            $startTimeDate = new Carbon($startTime);
            $slug = $slug . '-' . $startTimeDate->format('Y-m-d-H-i');
        }
        return $slug;
    }

    public static function generateSlugFromGame(Event $event): string
    {
        $slug = $event->homeParticipant->short_name .
            '-' . $event->awayParticipant->short_name .
            '-' . $event->start_time->format('Y-m-d-H-i');

        return $slug;
    }



}
