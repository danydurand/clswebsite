<?php

namespace App\Domain\StatusCode;

use App\Models\Event;
use App\Models\League;
use App\Models\ApiEvent;
use App\Models\Category;
use App\Classes\PResponse;
use App\Models\StatusCode;
use App\Models\Participant;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Domain\Event\EventServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Domain\League\LeagueServices;
use Illuminate\Support\Facades\Storage;

class StatusCodeServices
{


    public static function getStatusCode(ApiEvent $apiEvent): StatusCode
    {
        $statusCode = StatusCode::findByCode($apiEvent->status_code);

        if (!($statusCode instanceof StatusCode)) {

            $color     = 'danger';
            $image     = 'heroicon-o-star';
            $isChecked = false;
            if (array_key_exists($apiEvent->status_type, StatusCode::preset())) {
                $color     = StatusCode::preset()[$apiEvent->status_type]['color'];
                $image     = StatusCode::preset()[$apiEvent->status_type]['image'];
                $isChecked = true;
            }

            $statusCode = StatusCode::create([
                'code'        => $apiEvent->status_code,
                'type'        => $apiEvent->status_type,
                'description' => $apiEvent->status_description,
                'is_checked'  => $isChecked,
                'color'       => $color,
                'image'       => $image,
            ]);
        }

        return $statusCode;
    }




}
