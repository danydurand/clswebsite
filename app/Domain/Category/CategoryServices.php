<?php

namespace App\Domain\Category;

use App\Models\Bank;
use App\Models\Category;
use App\Models\User;
use App\Models\Seller;
use App\Classes\PResponse;
use App\Models\BankLottery;
use Illuminate\Support\Carbon;
use App\Domain\User\UserTypeEnum;
use App\Models\ConsortiumLottery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class CategoryServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Category::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
                UserTypeEnum::Banker->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return Category::byConsortium($consortiumId);
        }
        return Category::query()->where('id', '<', 0)
            ->take(1);
    }





}
