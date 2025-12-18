<?php

namespace App\Domain\Concept;

use App\Domain\User\UserTypeEnum;
use App\Models\Concept;


class ConceptServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Concept::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Concept::query()
                ->byConsortium($user->consortium_id);
        }
        return collect([]);

    }

}
