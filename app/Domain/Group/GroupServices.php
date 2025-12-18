<?php

namespace App\Domain\Group;

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

class GroupServices
{

    public static function assignProfile(array $ids, int $profileId, string $profileType): PResponse
    {
        $response = new PResponse();
        $qty = 0;
        try {
            if ($profileType == 'commission') {
                $qty = Group::whereIn('id', $ids)
                    ->update([
                        'commission_id' => $profileId,
                        'commission_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'limit') {
                $qty = Group::whereIn('id', $ids)
                    ->update([
                        'limit_id' => $profileId,
                        'limit_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'payment') {
                $qty = Group::whereIn('id', $ids)
                    ->update([
                        'payment_id' => $profileId,
                        'payment_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'restriction') {
                $qty = Group::whereIn('id', $ids)
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
            return Group::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Banker->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return Group::byConsortium($consortiumId);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = $user->id;
            return Group::bySupervisor($supervisorId);
        }
        return Group::query()->where('id', '<', 0)
            ->take(1);
    }

    public static function changeSupervisor(Collection $groups, int $supervisorId): PResponse
    {
        $response = new PResponse();
        try {
            $qty = Group::whereIn('id', $groups->pluck('id'))
                ->update(['supervisor_id' => $supervisorId]);
            $response->userMess = $qty . ' Group(s) moved successfully';
            info($response->userMess);
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }


    public static function generateCode(int $consortiumId): string
    {
        $next = Group::byConsortium($consortiumId)->count() + 1;
        return 'G' . $consortiumId . '-' . $next;
    }


}
