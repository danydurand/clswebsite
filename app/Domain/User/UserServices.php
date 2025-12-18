<?php

namespace App\Domain\User;

use App\Models\User;
use App\Domain\User\UserTypeEnum;

class UserServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return User::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return User::byConsortium($consortiumId);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankId = $user->bank_id;
            return User::byBank($bankId);
        }
        return [];
    }

}
