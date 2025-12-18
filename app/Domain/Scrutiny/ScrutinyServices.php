<?php

namespace App\Domain\Scrutiny;

use App\Models\User;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Scrutiny;
use App\Classes\PResponse;
use App\Models\Consortium;
use App\Models\TicketAction;
use App\Models\TicketDetail;
use App\Models\PaymentDetail;
use Illuminate\Support\Carbon;
use App\Domain\Services\TicketServices;
use Illuminate\Support\Facades\DB;
use App\Domain\Ticket\TicketStatusEnum;

class ScrutinyServices
{

    public static function generateCode(int $consortiumId): string
    {
        $date = Carbon::now();
        return 'S-' . $date->format('ymd') . '-' . $date->format('hi') . '-' . $consortiumId;
    }




}
