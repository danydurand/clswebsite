<?php

namespace App\Domain\Restriction;

use App\Models\Bank;
use App\Models\Game;
use App\Models\Group;
use App\Models\RestrictionDetail;
use App\Models\Seller;
use App\Models\Lottery;
use App\Classes\PResponse;
use App\Models\Restriction;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\BankRestriction;
use App\Models\GroupRestriction;
use App\Domain\User\UserTypeEnum;
use App\Models\SellerRestriction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestrictionServices
{

    public static function setInitialValues(Restriction $restriction): PResponse
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
                    $isActive = false;
                    $initDate = null;
                    $endDate = null;
                    $numbers = null;
                    $records[] = [
                        'restriction_id' => $restriction->id,
                        'specific_numbers' => $numbers,
                        'raffle_time' => $time,
                        'game_id' => $game->id,
                        'max_bet_amount' => 0,
                        'init_date' => $initDate,
                        'is_active' => $isActive,
                        'end_date' => $endDate,
                    ];
                }
            }
        }
        DB::beginTransaction();
        try {
            $qty = RestrictionDetail::insertOrIgnore($records);
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
            return Restriction::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Restriction::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            return Restriction::query()
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->byConsortium($user->consortium_id)
                            ->whereNull('banker_id');
                    })
                        ->orWhere('banker_id', $user->banker_id);
                });
        }
        return Restriction::query()->where('id', '<', 0)
            ->take(1);
    }

    public static function applyToGroups(?Restriction $restriction, array $groupIds): PResponse
    {
        info('');
        info('RestrictionServices::applyToGroups');
        info('==================================');
        info('Restriction: ' . $restriction->name);
        info('Applying restrictions to ' . count($groupIds) . ' groups');

        $response = new PResponse();
        $restrictionId = ($restriction) ? $restriction->id : null;
        try {
            $qty = Group::whereIn('id', $groupIds)
                ->update([
                    'restriction_id' => $restrictionId,
                    'restriction_assigned_at' => Carbon::now(),
                ]);
            //-------------------------------------------------------------------
            // Now we apply the restrictions to the banks related to the groups
            //-------------------------------------------------------------------
            $bankIds = Bank::whereIn('group_id', $groupIds)
                ->pluck('id')
                ->toArray();
            self::applyToBanks($restriction, $bankIds);

            info($qty . ' Groups restrictions applied');
            $response->userMess = $qty . ' Groups restrictions applied';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }

    public static function applyToBanks(?Restriction $restriction, array $bankIds): PResponse
    {
        info('');
        info('RestrictionServices::applyToBanks');
        info('=================================');
        info('Restriction: ' . $restriction->name);
        info('Applying restrictions to ' . count($bankIds) . ' banks');

        $response = new PResponse();
        $restrictionId = ($restriction) ? $restriction->id : null;
        try {
            $qty = Bank::whereIn('id', $bankIds)
                ->update([
                    'restriction_id' => $restrictionId,
                    'restriction_assigned_at' => Carbon::now(),
                ]);
            //--------------------------------------------------------------------
            // Now we apply the restrictions to the sellers related to the banks
            //--------------------------------------------------------------------
            $sellerIds = Seller::whereIn('bank_id', $bankIds)->pluck('id')->toArray();
            self::applyToSellers($restriction, $sellerIds);

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


    public static function applyToSellers(?Restriction $restriction, array $sellerIds): PResponse
    {
        info('');
        info('RestrictionServices::applyToSellers');
        info('===================================');
        info('Restriction: ' . $restriction->name);
        info('Applying restrictions to ' . count($sellerIds) . ' sellers');

        $response = new PResponse();
        $restrictionId = ($restriction) ? $restriction->id : null;
        try {
            $qty = Seller::whereIn('id', $sellerIds)
                ->update([
                    'restriction_id' => $restrictionId,
                    'restriction_assigned_at' => Carbon::now(),
                ]);

            info($qty . ' Sellers restrictions applied');
            $response->userMess = $qty . ' Sellers restrictions applied';
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
