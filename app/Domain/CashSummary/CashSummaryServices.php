<?php

namespace App\Domain\CashSummary;

use App\Models\CashSummary;
use App\Domain\User\UserTypeEnum;

class CashSummaryServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return CashSummary::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
            ])
        ) {
            return CashSummary::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return CashSummary::query()
                ->bySupervisor($user->id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            return CashSummary::query()
                ->byBank($user->bank_id);
        }
        return [];

    }


}
