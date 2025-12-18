<?php

namespace App\Domain\LotteryGame;

use DateTime;
use DateInterval;
use App\Models\Raffle;
use App\Models\Lottery;
use App\Classes\PResponse;
use App\Services\AuthUser;
use App\Models\LotteryGame;
use App\Models\DrawingResult;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Domain\Raffle\RaffleServices;
use App\Models\RaffleTimesDependence;

class LotteryGameServices
{

    public static function getDrawingDays(LotteryGame $lotteryGame): PResponse
    {
        $procName = 'Get Drawing Days';
        $response = new PResponse();
        //-------------------------------------------
        // Setting the rapidapi service parameters
        //-------------------------------------------
        $host     = config('lottery.rapidapi.host');
        $endpoint = config('lottery.endpoint.game-list-url') . $lotteryGame->lottery->code;
        $apiKey   = config('lottery.rapidapi.api-key');
        $apiUrl   = 'https://' . trim($host) . '/' . trim($endpoint);
        //----------------------------
        // Call the rapidapi service
        //----------------------------
        $activeDays = [];
        $stopSaleTime = null;
        $drawHour = null;
        try {
            $apiResponse = Http::withHeaders([
                'x-rapidapi-host' => $host,
                'x-rapidapi-key' => $apiKey,
            ])->withOptions([
                'verify' => true,
            ])->get($apiUrl);
            if ($apiResponse['message'] === 'ok') {
                $result       = $apiResponse->json();
                $data         = $result['data'][0];
                $stopSaleTime = $data['stopSaleTime'];
                $drawHour     = $data['drawTime'];
                //-------------------------------------
                // Updating the lottery game record
                //-------------------------------------
                $activeDays = self::getActiveDays($data['drawDays']);

                $lotteryGame->update([
                    'draw_days'      => $activeDays,
                    'draw_time'      => $data['drawTime'],
                    'stop_sale_time' => $data['stopSaleTime'],
                ]);

                if ($data['stopSaleTime'] < $stopSaleTime) {
                    $stopSaleTime = $data['stopSaleTime'];
                }
                if ($data['drawTime'] > $drawHour) {
                    $drawHour = $data['drawTime'];
                }

            } else {
                Log::error('Error calling the rapidapi service: ' . $apiResponse['message']);
                $response->indiOkey = false;
                $response->userMess .= $apiResponse['message'] . '<br>';
                $response->data = $lotteryGame->name;
                $response->qtyErrors++;
            }
        } catch (\Error $e) {
            Log::error('Error keeping the result in the DB: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->userMess .= $e->getMessage() . '<br>';
            $response->data = $lotteryGame->name;
            $response->qtyErrors++;
        }
        $send['drawDays']     = $activeDays;
        $send['stopSaleTime'] = $stopSaleTime;
        $send['drawHour']     = $drawHour;

        $response->data = $send;
        // info('Sending this data: ' . print_r($response->data, true));
        return $response;
    }


    public static function getActiveDays(array $daysArray): array {
        // Define the order of the days of the week
        $orderedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        // Initialize an empty array to hold the active days
        $activeDays = [];

        // Loop through the ordered days and check their values in the input array
        foreach ($orderedDays as $day) {
            if (isset($daysArray[$day]) && $daysArray[$day] > 0) {
                $activeDays[] = $day; // Add the day to the active days if its value is greater than 0
            }
        }

        return $activeDays; // Return the array of active days
    }


}
