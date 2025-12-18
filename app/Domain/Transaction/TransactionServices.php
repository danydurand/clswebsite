<?php

namespace App\Domain\Transaction;

use App\Domain\Route\RouteStatusEnum;
use App\Models\Bank;
use App\Models\Group;
use App\Models\Route;
use App\Classes\PResponse;
use App\Models\Supervisor;
use App\Models\Transaction;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\LoanDetail\FeeStatusEnum;
use App\Domain\Transaction\TransactionTypeEnum;


class TransactionServices
{

    public static function updateRouteStatus(Route $route)
    {
        $tranCount = $route->transac_count;
        $undoCount = $route->undone_transac_count;
        $doneCount = $route->done_transac_count;

        if ($doneCount == $tranCount) {
            $route->status = RouteStatusEnum::Finished->value;
            $route->save();
            return;
        }
        if ( ($doneCount > 0) && ($undoCount > 0) ) {
            $route->status = RouteStatusEnum::InProgress->value;
            $route->save();
            return;
        }
        if ($undoCount > 0) {
            $route->status = RouteStatusEnum::Pending->value;
            $route->save();
            return;
        }
    }


    public static function queryRecords($user)
    {
        if (in_array($user->type, [
            UserTypeEnum::Master->value,
            UserTypeEnum::Admin->value,
        ])) {
            return Transaction::query(); //->orderBy('id', 'desc');
        }
        if (in_array($user->type, [
            UserTypeEnum::Owner->value,
            UserTypeEnum::Secretary->value,
        ])) {
            $bankIds = Bank::byConsortium($user->consortium_id)->pluck('id');
            return Transaction::query()
                ->whereIn('bank_id', $bankIds);
                // ->orderBy('id', 'desc');
        }
        if (in_array($user->type, [
            UserTypeEnum::Supervisor->value,
        ])) {
            return Transaction::bySupervisor($user->id);
                // ->orderBy('id', 'desc');
        }
        return [];
    }

}
