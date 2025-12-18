<?php

namespace App\Domain\Bet;

use App\Models\Bet;
use App\Models\Bank;
use App\Models\User;
use App\Models\Event;
use App\Classes\PResponse;
use App\Services\AuthUser;
use Illuminate\Support\Str;
use App\Domain\Bet\BetTypeEnum;
use App\Domain\Bet\BetStatusEnum;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;
use App\Domain\Customer\CustomerServices;
use App\Domain\FinancialTransaction\TrxTypeEnum;

class BetServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
            ])
        ) {
            return Bet::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Bet::webSale();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            return Bet::query()
                ->byConsortium($user->consortium_id);

        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Bet::query()
                ->bySupervisor($user->id);

        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            return Bet::query()
                ->whereIn('bank_id', $bankIds);

        }
        return Bet::query()->where('id', '<', 0);

    }


    public static function generateCode(): string
    {
        return (string) Str::uuid();
    }


    public static function nullify(PResponse $response, $bets, string $reason): PResponse
    {
        if (!is_array($bets) && !($bets instanceof Collection)) {
            $bets = [$bets];
        }

        info('There are ' . count($bets) . ' bets to nullify');

        $toBeNotified = collect([]);
        $qtyBets = 0;
        $qtyAlreadyNullified = 0;
        foreach ($bets as $bet) {
            if ($bet->status->value === BetStatusEnum::Nullified->value) {
                // Already nullified, skip
                info('Bet ID: ' . $bet->id . ' is already nullified, skipping.');
                $qtyAlreadyNullified++;
                continue;
            }
            try {
                // Revert customer's balance if the bet was a winner
                if ($bet->status->value === BetStatusEnum::Win->value && $bet->return_amount > 0) {
                    // Find and delete the financial transaction
                    $transaction = FinancialTransaction::where('trx', 'BET-' . $bet->id)->first();
                    if ($transaction) {
                        // Revert the customer's balance
                        $bet->customer->balance -= $bet->return_amount;
                        $bet->customer->save();
                        // Here we create a reversing transaction record for audit purposes
                        FinancialTransaction::create([
                            'customer_id' => $bet->customer_id,
                            'amount' => 0,
                            'charge' => $bet->return_amount,
                            'post_balance' => $bet->customer->balance,
                            'trx_type' => TrxTypeEnum::Debit->value,
                            'trx' => 'REVERSAL-BET-' . $bet->id,
                            'remark' => 'Reversal of winning bet ID ' . $bet->id,
                            'detail' => 'Nullification of winning bet',
                            'data' => json_encode(['original_trx' => $transaction->trx]),
                        ]);
                    }
                }

                $bet->return_amount = 0;
                $bet->status = BetStatusEnum::Nullified->value;
                $bet->nullify_reason = $reason;
                $bet->result_time = null;
                $bet->save();

                foreach ($bet->betDetails as $betDetail) {
                    $betDetail->status = BetStatusEnum::Nullified->value;
                    $betDetail->save();
                }
                $response->qtyProcessed++;

                info("Bet ID: {$bet->id} nullified");

                // We store the Customers to be notified later
                $toBeNotified->add($bet);

            } catch (\Exception $e) {
                Log::error('Error nullifying bet id' . $bet->id . ': ' . $e->getMessage());
                $response->qtyErrors++;
                $response->errors[] = [
                    'reference' => 'Bet Id: ' . $bet->id,
                    'message' => 'Error nullifying',
                    'comment' => $e->getMessage(),
                ];
            }
            $qtyBets++;
        }
        $response->okay = $response->qtyErrors === 0;
        info('Finished nullifying bets');

        // Notify affected customers
        CustomerServices::sendNullifyNotification($toBeNotified);

        $response->userMess = $response->qtyProcessed . " Bets Nullified.";
        if ($qtyAlreadyNullified > 0) {
            $response->userMess .= ' <br> ' . $qtyAlreadyNullified . " Already Nullified";
        }
        if ($response->qtyErrors > 0) {
            $response->userMess .= ' <br> ' . $response->qtyErrors . " Errors";
        }

        return $response;
    }



    public static function totalWinnerBets()
    {
        return Bet::winner()->count();
    }

    public static function totalLoserBets()
    {
        return Bet::loser()->count();
    }

    public static function totalPendingBets()
    {
        return Bet::pending()->count();
    }

    public static function totalRefundedBets()
    {
        return Bet::refunded()->count();
    }

    public static function winnerBets()
    {
        $userType = Auth::user()->type;
        $winnerQty = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $winnerQty = Bet::today()
                ->winner()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $winnerQty = Bet::today()
                ->byConsortium($consortiumId)
                ->winner()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $winnerQty = Bet::today()
                ->bySupervisor($supervisorId)
                ->winner()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $winnerQty = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->winner()
                ->count();
        }

        return $winnerQty;

    }


    public static function cancelledBets()
    {
        $userType = Auth::user()->type;
        $cancelledQty = 0;

        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $cancelledQty = Bet::today()
                ->cancelled()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $cancelledQty = Bet::today()
                ->byConsortium($consortiumId)
                ->cancelled()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $cancelledQty = Bet::today()
                ->bySupervisor($supervisorId)
                ->cancelled()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $cancelledQty = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->cancelled()
                ->count();
        }

        return $cancelledQty;

    }

    public static function soldBets()
    {
        $userType = Auth::user()->type;
        $soldQty = 0;

        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $soldQty = Bet::today()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $soldQty = Bet::today()
                ->byConsortium($consortiumId)
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $soldQty = Bet::today()
                ->bySupervisor($supervisorId)
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $soldQty = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->count();
        }

        return $soldQty;

    }


    public static function todaySoldAmount()
    {
        $userType = Auth::user()->type;
        $soldAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $soldAmount = Bet::today()
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $soldAmount = Bet::today()
                ->byConsortium($consortiumId)
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $soldAmount = Bet::today()
                ->bySupervisor($supervisorId)
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $soldAmount = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }

        return $soldAmount;
    }

    public static function todayPrizeAmount()
    {
        $userType = Auth::user()->type;
        $prizeAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $prizeAmount = Bet::today()
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $prizeAmount = Bet::today()
                ->byConsortium($consortiumId)
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $prizeAmount = Bet::today()
                ->bySupervisor($supervisorId)
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $prizeAmount = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }

        return $prizeAmount;
    }

    public static function todayProfit()
    {
        $userType = Auth::user()->type;
        $profitAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $profitAmount = Bet::today()
                ->notCancelled()
                ->sum('profit') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $profitAmount = Bet::today()
                ->byConsortium($consortiumId)
                ->notCancelled()
                ->sum('profit') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $profitAmount = Bet::today()
                ->bySupervisor($supervisorId)
                ->notCancelled()
                ->sum('profit') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $profitAmount = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('profit') / 100;
        }

        return $profitAmount;
    }



}
