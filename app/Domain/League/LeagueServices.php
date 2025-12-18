<?php

namespace App\Domain\League;

use App\Models\Game;
use App\Models\User;
use App\Models\League;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Scrutiny;
use App\Classes\PResponse;
use App\Models\Consortium;
use Illuminate\Support\Str;
use App\Models\TicketDetail;
use App\Models\PaymentDetail;
use Illuminate\Support\Carbon;
use App\Domain\Services\TicketServices;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ResultWinnerSequence;
use Illuminate\Support\Facades\Auth;
use App\Domain\Ticket\TicketStatusEnum;
use App\Domain\Scrutiny\ScrutinyServices;

class LeagueServices
{

    public static function getShortName(string $leagueSlug, int $categoryId): string
    {
        $qty = League::count();
        return str_pad($qty, 5, '0', STR_PAD_LEFT);
        // $qty = 3;
        // while (true) {
        //     $shortName = substr($leagueSlug, 0, $qty);
        //     $exists    = League::findByShortNameAndCategory($shortName, $categoryId);
        //     if (!$exists) {
        //         break;
        //     }
        //     $qty++;
        // }
        // return $shortName;
    }



}
