<?php

namespace App\Domain\Stats;

use App\Models\Bet;
use App\Models\Bank;
use App\Models\User;
use App\Models\Consortium;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\Auth;

class SportsStatsServices
{

    /**
     * Summary of todayStakeAmount in Sports Business
     * @return float|int
     */
    public static function todayStakeAmount(?Consortium $consortium = null)
    {
        $userType = Auth::user()->type;
        $stakeAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            if ($consortium) {
                $stakeAmount = Bet::today()
                    ->byConsortium($consortium->id)
                    ->notNullified()
                    ->sum('stake_amount') / 100;
            } else {
                $stakeAmount = Bet::today()
                    ->notNullified()
                    ->sum('stake_amount') / 100;
            }
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $stakeAmount = Bet::today()
                ->byConsortium($consortiumId)
                ->notNullified()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $stakeAmount = Bet::today()
                ->bySupervisor($supervisorId)
                ->notNullified()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $stakeAmount = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->notNullified()
                ->sum('stake_amount') / 100;
        }

        return $stakeAmount;
    }

    /**
     * Summary of todayReturnAmount in Lottery Business
     * @return float|int
     */
    public static function todayReturnAmount(?Consortium $consortium = null)
    {
        $userType = Auth::user()->type;
        $returnAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            if ($consortium) {
                $returnAmount = Bet::today()
                    ->byConsortium($consortium->id)
                    ->winner()
                    ->sum('return_amount') / 100;
            } else {
                $returnAmount = Bet::today()
                    ->winner()
                    ->sum('return_amount') / 100;
            }
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $returnAmount = Bet::today()
                ->byConsortium($consortiumId)
                ->winner()
                ->sum('return_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $returnAmount = Bet::today()
                ->bySupervisor($supervisorId)
                ->winner()
                ->sum('return_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $returnAmount = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->winner()
                ->sum('return_amount') / 100;
        }

        return $returnAmount;
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

    public static function oneMonthBeforewinnerBets()
    {
        return Bet::oneMonthBefore()->winner()->count();
    }

    public static function nullifiedBets()
    {
        $userType = Auth::user()->type;
        $nullifiedQty = 0;

        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $nullifiedQty = Bet::today()
                ->nullified()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $nullifiedQty = Bet::today()
                ->byConsortium($consortiumId)
                ->nullified()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $nullifiedQty = Bet::today()
                ->bySupervisor($supervisorId)
                ->nullified()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $nullifiedQty = Bet::today()
                ->whereIn('bank_id', $bankIds)
                ->nullified()
                ->count();
        }

        return $nullifiedQty;

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

    public static function unvCustomers()
    {
        return User::unverified()->count();
    }

    public static function activeCustomers()
    {
        return User::active()->count();
    }

    public static function totalCustomers()
    {
        return User::customer()->count();
    }


    /**
     * Summary of todayProfit in Sports Business
     * @return float|int
     */
    public static function todayProfit(?Consortium $consortium = null)
    {
        $userType = Auth::user()->type;
        $profitAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            if ($consortium) {
                $profitAmount = Bet::today()
                    ->byConsortium($consortium->id)
                    ->notNullified()
                    ->sum('profit') / 100;
            } else {
                $profitAmount = Bet::today()
                    ->notNullified()
                    ->sum('profit') / 100;
            }
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
                ->notNullified()
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
                ->notNullified()
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
                ->notNullified()
                ->sum('profit') / 100;
        }

        return $profitAmount;
    }

    public static function thisMonthSoldAmount()
    {
        return bcdiv(Bet::thisMonth()->sum('stake_amount') / 100, 2);
    }

    public static function thisMonthPrizeAmount()
    {
        return bcdiv(Bet::thisMonth()->winner()->sum('prize_amount') / 100, 2);
    }

    public static function thisMonthProfit()
    {
        return bcsub(StatsServices::thisMonthSoldAmount(), StatsServices::thisMonthPrizeAmount(), 2);
    }

    public static function oneMonthBeforeSoldAmount()
    {
        return bcdiv(Bet::oneMonthBefore()->sum('stake_amount') / 100, 2);
    }

    public static function oneMonthBeforePrizeAmount()
    {
        return bcdiv(Bet::oneMonthBefore()->winner()->sum('prize_amount') / 100, 2);
    }

    public static function oneMonthBeforeProfit()
    {
        return bcsub(StatsServices::oneMonthBeforeSoldAmount(), StatsServices::oneMonthBeforePrizeAmount(), 2);
    }



}
