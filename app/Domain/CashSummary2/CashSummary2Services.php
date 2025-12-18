<?php

namespace App\Domain\CashSummary2;

use App\Models\CashSummary2;
use App\Domain\User\UserTypeEnum;

class CashSummary2Services
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return CashSummary2::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
            ])
        ) {
            return CashSummary2::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return CashSummary2::query()
                ->bySupervisor($user->id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            return CashSummary2::query()
                ->byBank($user->bank_id);
        }
        return collect([]);

    }


}
