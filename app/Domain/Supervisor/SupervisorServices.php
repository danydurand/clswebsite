<?php

namespace App\Domain\Supervisor;

use App\Domain\User\UserTypeEnum;
use App\Models\Supervisor;

class SupervisorServices
{

    public static function queryRecords($user)
    {
        if ($user->type == UserTypeEnum::Master->value) {
            $query = Supervisor::query();
        } else {
            $consortiumId = $user->consortium_id ?? 1;
            info('Looking for the Supervisors or the Consortium: ' . $consortiumId);
            $query = Supervisor::byConsortium($consortiumId);
        }
        return $query;
    }

}
