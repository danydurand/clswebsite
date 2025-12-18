<?php

namespace App\Domain\ConsortiumLottery;

use App\Domain\User\UserTypeEnum;
use App\Models\Concept;
use App\Models\ConsortiumLottery;


class ConsortiumLotteryServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return ConsortiumLottery::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            return ConsortiumLottery::query()
                ->byConsortium($user->consortium_id);
        }
        return [];

    }

}
