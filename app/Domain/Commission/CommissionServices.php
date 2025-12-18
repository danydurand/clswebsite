<?php

namespace App\Domain\Commission;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Game;
use App\Models\Group;
use App\Models\Seller;
use App\Models\Lottery;
use App\Classes\PResponse;
use App\Models\Commission;
use App\Models\CommissionDetail;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\Log;

class CommissionServices
{

    public static function setInitialValues(Commission $commission, int $value): PResponse
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
                    $records[] = [
                        'commission_id' => $commission->id,
                        'raffle_time' => $time,
                        'game_id' => $game->id,
                        'commission_perc' => $value,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }
        try {
            $qty = CommissionDetail::insertOrIgnore($records);
            $response->userMess = $qty . ' ' . __('Initial values set !!!');
        } catch (\Error $e) {
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
            return Commission::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Commission::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            // info('User: '.$user->toJson());
            $query = Commission::query()
                ->where(function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('consortium_id', $user->consortium_id)
                            ->whereNull('banker_id');
                    })
                        ->orWhere('banker_id', $user->banker_id);
                });
            // dd($query->toSql());
            return $query;
        }
        return collect([]);

    }


    public static function applyToGroups(?Commission $commission, array $groupIds): PResponse
    {
        info('');
        info('CommissionServices::applyToGroups');
        info('==================================');
        info('Commission: ' . $commission->name);
        info('Applying commissions to ' . count($groupIds) . ' groups');

        $response = new PResponse();
        $commissionId = ($commission) ? $commission->id : null;
        try {
            $qty = Group::whereIn('id', $groupIds)
                ->update([
                    'commission_id' => $commissionId,
                    'commission_assigned_at' => Carbon::now(),
                ]);
            //-------------------------------------------------------------------
            // Now we apply the restrictions to the banks related to the groups
            //-------------------------------------------------------------------
            $bankIds = Bank::whereIn('group_id', $groupIds)
                ->pluck('id')->toArray();
            self::applyToBanks($commission, $bankIds);

            info($qty . ' Groups commissions applied');
            $response->userMess = $qty . ' Groups commissions applied';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }

    public static function applyToBanks(?Commission $commission, array $bankIds): PResponse
    {
        info('');
        info('CommissionServices::applyToBanks');
        info('================================');
        info('Commission: ' . $commission->name);
        info('Applying commissions to ' . count($bankIds) . ' banks');

        $response = new PResponse();
        $commissionId = ($commission) ? $commission->id : null;
        try {
            $qty = Bank::whereIn('id', $bankIds)
                ->update([
                    'commission_id' => $commissionId,
                    'commission_assigned_at' => Carbon::now(),
                ]);
            //--------------------------------------------------------------------
            // Now we apply the restrictions to the sellers related to the banks
            //--------------------------------------------------------------------
            $sellerIds = Seller::whereIn('bank_id', $bankIds)->pluck('id')->toArray();
            self::applyToSellers($commission, $sellerIds);

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


    public static function applyToSellers(?Commission $commission, array $sellerIds): PResponse
    {
        info('');
        info('CommissionServices::applyToSellers');
        info('==================================');
        info('Commission: ' . $commission->name);
        info('Applying commissions to ' . count($sellerIds) . ' sellers');

        $response = new PResponse();
        $commissionId = ($commission) ? $commission->id : null;
        try {
            $qty = Seller::whereIn('id', $sellerIds)
                ->update([
                    'commission_id' => $commissionId,
                    'commission_assigned_at' => Carbon::now(),
                ]);
            info($qty . ' Sellers commissions applied');
            $response->userMess = $qty . ' Sellers commissions applied';
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
