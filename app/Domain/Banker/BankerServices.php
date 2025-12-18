<?php

namespace App\Domain\Banker;

use App\Models\Banker;
use App\Models\Game;
use App\Models\Group;
use App\Models\Raffle;
use App\Classes\PResponse;
use App\Models\Supervisor;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ResultWinnerSequence;

class BankerServices
{

    public static function assignProfile(array $ids, int $profileId, string $profileType): PResponse
    {
        $response = new PResponse();
        $qty = 0;
        try {
            if ($profileType == 'commission') {
                $qty = Banker::whereIn('id', $ids)
                    ->update([
                        'commission_id' => $profileId,
                        'commission_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'limit') {
                $qty = Banker::whereIn('id', $ids)
                    ->update([
                        'limit_id' => $profileId,
                        'limit_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'payment') {
                $qty = Banker::whereIn('id', $ids)
                    ->update([
                        'payment_id' => $profileId,
                        'payment_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'restriction') {
                $qty = Banker::whereIn('id', $ids)
                    ->update([
                        'restriction_id' => $profileId,
                        'restriction_assigned_at' => now(),
                    ]);
            }
            $response->userMess = $qty . ' Banker(s) updated successfully';
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
            return Banker::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return Banker::byConsortium($consortiumId);
        }
        return Banker::query()->where('id', '<', 0);
    }

    public static function generateCode(int $consortiumId): string
    {
        $next = Banker::byConsortium($consortiumId)->count() + 1;
        return 'B' . $consortiumId . '-' . $next;
    }


}
