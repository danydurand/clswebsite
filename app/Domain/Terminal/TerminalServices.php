<?php

namespace App\Domain\Terminal;

use App\Models\Bank;
use App\Models\Terminal;
use App\Domain\User\UserTypeEnum;

class TerminalServices
{

    public static function queryRecords($user)
    {
        if ($user->type == UserTypeEnum::Master->value) {
            info('Looking for all the Sellers');
            return Terminal::query();
        }
        if ($user->type == UserTypeEnum::Owner->value) {
            $consortiumId = $user->consortium_id ?? 1;
            info('Looking for the Sellers or the Consortium: ' . $consortiumId);
            return Terminal::byConsortium($consortiumId);
        }
        $bankId = $user->bank_id ?? 1;
        info('Looking for the Sellers or the Bank: ' . $bankId);
        return Terminal::byBank($bankId);
    }

}
