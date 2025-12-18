<?php

namespace App\Domain\Payment;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Game;
use App\Models\Group;
use App\Models\Seller;
use App\Models\Lottery;
use App\Models\Payment;
use App\Classes\PResponse;
use App\Models\PaymentDetail;
use App\Domain\Game\GameServices;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentServices
{

    public static function setInitialValues(Payment $payment): PResponse
    {
        $response = new PResponse();
        $response->indiOkey = true;
        $response->qtyErrors = 0;

        $records = [];
        $lotteries = Lottery::all();
        foreach ($lotteries as $lottery) {
            // info('Setting initial values for Lottery: '.$lottery->name);
            $arrayTimes = $lottery->times;
            foreach ($arrayTimes as $time) {
                $games = Game::all();
                foreach ($games as $game) {
                    // info('Setting initial values for Game: '.$game->name);
                    $gameWinnerSequences = $game->gameWinnerSequences;
                    foreach ($gameWinnerSequences as $ws) {
                        $winnigFactor = $ws->winning_factor * 100;
                        $records[] = [
                            'payment_id' => $payment->id,
                            'raffle_time' => $time,
                            'game_id' => $game->id,
                            'winner_position' => $ws->position_order,
                            'winning_factor' => $winnigFactor,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                }
            }
        }
        DB::beginTransaction();
        try {
            // info('Creating: '.count($records).' records');
            PaymentDetail::insertOrIgnore($records);
            $payment->explanation = GameServices::expl();
            $payment->save();
            DB::commit();
            $qty = $payment->details()->count();
            $response->userMess = $qty . ' ' . __('Initial values set !!!');
            // info($response->userMess);
        } catch (\Error $e) {
            DB::rollback();
            $response->userMess = $e->getMessage();
            $response->indiOkey = true;
            $response->qtyErrors = 1;
            Log::error('Error setting initial values. ' . $response->userMess);
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
            return Payment::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Payment::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            return Payment::query()
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->byConsortium($user->consortium_id)
                            ->whereNull('banker_id');
                    })
                        ->orWhere('banker_id', $user->banker_id);
                });
        }
        return Payment::query()->where('id', '<', 0)
            ->take(1);
    }

    public static function applyToGroups(?Payment $payment, array $groupIds): PResponse
    {
        info('');
        info('PaymentServices::applyToGroups');
        info('==============================');
        info('Payment: ' . $payment->name);
        info('Applying payments to ' . count($groupIds) . ' groups');

        $response = new PResponse();
        $paymentId = ($payment) ? $payment->id : null;
        try {
            $qty = Group::whereIn('id', $groupIds)
                ->update([
                    'payment_id' => $paymentId,
                    'payment_assigned_at' => Carbon::now(),
                ]);
            //-------------------------------------------------------------------
            // Now we apply the restrictions to the banks related to the groups
            //-------------------------------------------------------------------
            $bankIds = Bank::whereIn('group_id', $groupIds)
                ->pluck('id')->toArray();
            self::applyToBanks($payment, $bankIds);

            info($qty . ' Groups payments applied');
            $response->userMess = $qty . ' Groups payments applied';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }

    public static function applyToBanks(?Payment $payment, array $bankIds): PResponse
    {
        info('');
        info('PaymentServices::applyToBanks');
        info('=============================');
        info('Payment: ' . $payment->name);
        info('Applying payments to ' . count($bankIds) . ' banks');

        $response = new PResponse();
        $paymentId = ($payment) ? $payment->id : null;
        try {
            $qty = Bank::whereIn('id', $bankIds)
                ->update([
                    'payment_id' => $paymentId,
                    'payment_assigned_at' => Carbon::now(),
                ]);
            //--------------------------------------------------------------------
            // Now we apply the restrictions to the sellers related to the banks
            //--------------------------------------------------------------------
            $sellerIds = Seller::whereIn('bank_id', $bankIds)->pluck('id')->toArray();
            self::applyToSellers($payment, $sellerIds);

            info($qty . ' Banks restrictions applied');
            $response->userMess = $qty . ' Banks restrictions applied';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }


    public static function applyToSellers(?Payment $payment, array $sellerIds): PResponse
    {
        info('');
        info('PaymentServices::applyToSellers');
        info('===============================');
        info('Payment: ' . $payment->name);
        info('Applying payments to ' . count($sellerIds) . ' sellers');

        $response = new PResponse();
        $paymentId = ($payment) ? $payment->id : null;
        try {
            $qty = Seller::whereIn('id', $sellerIds)
                ->update([
                    'payment_id' => $paymentId,
                    'payment_assigned_at' => Carbon::now(),
                ]);
            info($qty . ' Sellers payments applied');
            $response->userMess = $qty . ' Sellers payments applied';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }


}
