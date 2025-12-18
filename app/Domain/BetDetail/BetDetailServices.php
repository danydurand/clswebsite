<?php

namespace App\Domain\BetDetail;

use App\Models\Bet;
use App\Models\Bank;
use App\Models\User;
use App\Services\AuthUser;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\Auth;

class BetDetailServices
{


    public static function getFractionalOdds(string $odds): float
    {
       if (!str_contains($odds, '/')) {
           return (float) $odds;
       }

       list($numerator, $denominator) = explode('/', $odds);

       if ($denominator == 0) {
           return 1.0; // Or handle as an error
       }

       return ($numerator / $denominator) + 1;
   }



}
