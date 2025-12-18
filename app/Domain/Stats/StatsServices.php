<?php

namespace App\Domain\Stats;

use App\Models\Bank;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Consortium;
use App\Services\AuthUser;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\Auth;

class StatsServices
{


    public static function winnerTickets()
    {
        $userType = Auth::user()->type;
        $winnerQty = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $winnerQty = Ticket::today()
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
            $winnerQty = Ticket::today()
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
            $winnerQty = Ticket::today()
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
            $winnerQty = Ticket::today()
                ->whereIn('bank_id', $bankIds)
                ->winner()
                ->count();
        }

        return $winnerQty;

    }

    public static function oneMonthBeforewinnerTickets()
    {
        return Ticket::oneMonthBefore()->winner()->count();
    }

    public static function cancelledTickets()
    {
        $userType = Auth::user()->type;
        $cancelledQty = 0;

        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $cancelledQty = Ticket::today()
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
            $cancelledQty = Ticket::today()
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
            $cancelledQty = Ticket::today()
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
            $cancelledQty = Ticket::today()
                ->whereIn('bank_id', $bankIds)
                ->cancelled()
                ->count();
        }

        return $cancelledQty;

    }

    public static function soldTickets()
    {
        $userType = Auth::user()->type;
        $soldQty = 0;

        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $soldQty = Ticket::today()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $soldQty = Ticket::today()
                ->byConsortium($consortiumId)
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $soldQty = Ticket::today()
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
            $soldQty = Ticket::today()
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
     * Summary of todaySoldAmount in Lottery Business
     * @return float|int
     */
    public static function todaySoldAmount(?Consortium $consortium = null)
    {
        $userType = Auth::user()->type;
        $soldAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            if ($consortium) {
                $soldAmount = Ticket::today()
                    ->notCancelled()
                    ->byConsortium($consortium->id)
                    ->sum('stake_amount') / 100;
            } else {
                $soldAmount = Ticket::today()
                    ->notCancelled()
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
            $soldAmount = Ticket::today()
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
            $soldAmount = Ticket::today()
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
            $soldAmount = Ticket::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }

        return $soldAmount;
    }

    /**
     * Summary of todayPrizeAmount in Lottery Business
     * @return float|int
     */
    public static function todayPrizeAmount(?Consortium $consortium = null)
    {
        $userType = Auth::user()->type;
        $prizeAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            if ($consortium) {
                $prizeAmount = Ticket::today()
                    ->notCancelled()
                    ->byConsortium($consortium->id)
                    ->sum('prize_amount') / 100;
            } else {
                $prizeAmount = Ticket::today()
                    ->notCancelled()
                    ->sum('prize_amount') / 100;
            }
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $prizeAmount = Ticket::today()
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
            $prizeAmount = Ticket::today()
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
            $prizeAmount = Ticket::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }

        return $prizeAmount;
    }

    /**
     * Summary of todayProfit in Lottery Business
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
                $profitAmount = Ticket::today()
                    ->notCancelled()
                    ->byConsortium($consortium->id)
                    ->sum('profit') / 100;
            } else {
                $profitAmount = Ticket::today()
                    ->notCancelled()
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
            $profitAmount = Ticket::today()
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
            $profitAmount = Ticket::today()
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
            $profitAmount = Ticket::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('profit') / 100;
        }

        return $profitAmount;
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
        return bcsub(StatsServices::thisMonthSoldAmount(), StatsServices::thisMonthPrizeAmount(), 2);
    }

    public static function oneMonthBeforeSoldAmount()
    {
        return bcdiv(Ticket::oneMonthBefore()->sum('stake_amount') / 100, 2);
    }

    public static function oneMonthBeforePrizeAmount()
    {
        return bcdiv(Ticket::oneMonthBefore()->winner()->sum('prize_amount') / 100, 2);
    }

    public static function oneMonthBeforeProfit()
    {
        return bcsub(StatsServices::oneMonthBeforeSoldAmount(), StatsServices::oneMonthBeforePrizeAmount(), 2);
    }



}
