<?php

namespace App\Domain\Limit;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Game;
use App\Models\Group;
use App\Models\Limit;
use App\Models\Seller;
use App\Models\Lottery;
use App\Classes\PResponse;
use App\Models\Commission;
use App\Models\LimitDetail;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LimitServices
{


    public static function setInitialValues(Limit $limit): PResponse
    {
        $response = new PResponse();
        $response->indiOkey = true;
        $response->qtyErrors = 0;

        $records = [];

        $lotteries = Lottery::all();
        foreach ($lotteries as $lottery) {
            $arrayTimes = $lottery->times;
            foreach ($arrayTimes as $time) {
                $games = Game::all();
                foreach ($games as $game) {
                    $max = match ($game->pick) {
                        2 => 50,
                        3 => 80,
                        4 => 100,
                        5 => 100,
                        default => 50,
                    };
                    $records[] = [
                        'limit_id' => $limit->id,
                        'raffle_time' => $time,
                        'game_id' => $game->id,
                        'max_amount' => $max * 100,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        DB::beginTransaction();
        try {
            $qty = LimitDetail::insertOrIgnore($records);
            $response->userMess = $qty . ' ' . __('Initial values set !!!');
            DB::commit();
        } catch (\Error $e) {
            DB::rollback();
            $response->userMess = $e->getMessage();
            $response->indiOkey = true;
            $response->qtyErrors = 0;
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
            return Limit::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Limit::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            return Limit::query()
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->byConsortium($user->consortium_id)
                            ->whereNull('banker_id');
                    })
                        ->orWhere('banker_id', $user->banker_id);
                });
        }
        return Limit::query()->where('id', '<', 0)
            ->take(1);

    }


    public static function applyToGroups(?Limit $limit, array $groupIds): PResponse
    {
        info('');
        info('LimitServices::applyToGroups');
        info('============================');
        info('Limit: ' . $limit->name);
        info('Applying limits to ' . count($groupIds) . ' groups');

        $response = new PResponse();
        $limitId = ($limit) ? $limit->id : null;
        try {
            $qty = Group::whereIn('id', $groupIds)
                ->update([
                    'limit_id' => $limitId,
                    'limit_assigned_at' => Carbon::now(),
                ]);
            //-------------------------------------------------------------------
            // Now we apply the restrictions to the banks related to the groups
            //-------------------------------------------------------------------
            $bankIds = Bank::whereIn('group_id', $groupIds)
                ->pluck('id')->toArray();
            self::applyToBanks($limit, $bankIds);

            info($qty . ' Groups limits applied');
            $response->userMess = $qty . ' Groups limits applied';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }

    public static function applyToBanks(?Limit $limit, array $bankIds): PResponse
    {
        info('');
        info('LimitServices::applyToBanks');
        info('===========================');
        info('Limit: ' . $limit->name);
        info('Applying limits to ' . count($bankIds) . ' banks');

        $response = new PResponse();
        $limitId = ($limit) ? $limit->id : null;
        try {
            $qty = Bank::whereIn('id', $bankIds)
                ->update([
                    'limit_id' => $limitId,
                    'limit_assigned_at' => Carbon::now(),
                ]);
            //--------------------------------------------------------------------
            // Now we apply the limits to the sellers related to the banks
            //--------------------------------------------------------------------
            $sellerIds = Seller::whereIn('bank_id', $bankIds)->pluck('id')->toArray();
            self::applyToSellers($limit, $sellerIds);

            info($qty . ' Banks limits applied');
            $response->userMess = $qty . ' Banks limits applied';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }


    public static function applyToSellers(?Limit $limit, array $sellerIds): PResponse
    {
        info('');
        info('LimitServices::applyToSellers');
        info('=============================');
        info('Limit: ' . $limit->name);
        info('Applying limits to ' . count($sellerIds) . ' sellers');

        $response = new PResponse();
        $limitId = ($limit) ? $limit->id : null;
        try {
            $qty = Seller::whereIn('id', $sellerIds)
                ->update([
                    'limit_id' => $limitId,
                    'limit_assigned_at' => Carbon::now(),
                ]);
            info($qty . ' Sellers limits applied');
            $response->userMess = $qty . ' Sellers limits applied';
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
