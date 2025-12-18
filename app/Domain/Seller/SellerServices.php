<?php

namespace App\Domain\Seller;

use App\Models\Bank;
use App\Models\Seller;
use App\Classes\PResponse;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\Log;

class SellerServices
{

    public static function assignProfile(array $ids, int $profileId, string $profileType): PResponse
    {
        $response = new PResponse();
        $qty = 0;
        try {
            if ($profileType == 'commission') {
                $qty = Seller::whereIn('id', $ids)
                    ->update([
                        'commission_id' => $profileId,
                        'commission_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'limit') {
                $qty = Seller::whereIn('id', $ids)
                    ->update([
                        'limit_id' => $profileId,
                        'limit_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'payment') {
                $qty = Seller::whereIn('id', $ids)
                    ->update([
                        'payment_id' => $profileId,
                        'payment_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'restriction') {
                $qty = Seller::whereIn('id', $ids)
                    ->update([
                        'restriction_id' => $profileId,
                        'restriction_assigned_at' => now(),
                    ]);
            }
            $response->userMess = $qty . ' Group(s) updated successfully';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
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
            return Seller::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return Seller::byConsortium($consortiumId);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = $user->id;
            return Seller::bySupervisor($supervisorId);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = $user->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            return Seller::whereIn('bank_id', $bankIds);
        }
        return Seller::query()->latest()->take(1);
    }

}
