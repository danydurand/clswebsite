<?php

namespace App\Domain\Check;

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
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FinancialTransaction;
use App\Domain\FinancialTransaction\TrxTypeEnum;

class CheckServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Check::whereNull('consortium_id');
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
            ])
        ) {
            return Check::byConsortium($user->consortium_id);
        }
        return Check::query()->where('id', '-1');

    }


    public static function updateTotals(PResponse $response, $checks): PResponse
    {

        info('');
        info('Getting into CheckServices::updateTotals');
        info('========================================');
        info('');
        info('Response: ' . $response);

        if (!is_array($checks) && !$checks instanceof Collection) {
            $checks = collect([$checks]);
        }

        foreach ($checks as $check) {

            info('Check Id: ' . $check->id);

            //----------------------------------------------------------------------
            // Totalizing the checked events in order to update the Check process
            //----------------------------------------------------------------------
            $eventTotals = Event::byCheck($check->id)
                ->selectRaw(
                    'COALESCE(SUM(qty_bets),0) AS qty_bets,
                COALESCE(SUM(qty_winners),0) AS qty_winners,
                COALESCE(SUM(qty_losers),0) AS qty_losers,
                COALESCE(SUM(total_stake_amount),0) AS total_stake_amount,
                COALESCE(SUM(total_return_amount),0) AS total_return_amount,
                COALESCE(SUM(profit),0) AS profit'
                )
                ->first();

            info('eventTotals: ' . $eventTotals);

            //-----------------------------
            // Updating the Check process
            //-----------------------------
            $check->update([
                'qty_checked_events' => $response->qtyProcessed,
                'qty_bets' => $eventTotals->qty_bets,
                'qty_winners' => $eventTotals->qty_winners,
                'qty_losers' => $eventTotals->qty_losers,
                'total_stake_amount' => $eventTotals->total_stake_amount,
                'total_return_amount' => $eventTotals->total_return_amount,
                'profit' => $eventTotals->profit,
            ]);

            info('Check after update: ' . $check);
        }

        return $response;
    }



}
