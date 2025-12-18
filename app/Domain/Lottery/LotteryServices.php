<?php

namespace App\Domain\Lottery;

use DateTime;
use DateInterval;
use App\Models\User;
use App\Models\Raffle;
use App\Models\Lottery;
use App\Classes\PResponse;
use App\Services\AuthUser;
use App\Models\LotteryGame;
use App\Models\DrawingResult;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Domain\Raffle\RaffleServices;
use App\Models\RaffleTimesDependence;
use App\Domain\LotteryGame\LotteryGameServices;

class LotteryServices
{

    public static function lookForDrawingResults($raffleDate=null): PResponse
    {
        $response = new PResponse();
        //---------------------------------------------------------------------
        // Setting the current hour, date and the rapidapi service parameters
        //---------------------------------------------------------------------
        $currentHour = now()->format('H:i');
        $raffleDate  = $raffleDate ?? now()->toDateString();
        $apiKey      = config('lottery.rapidapi.api-key');
        $host        = config('lottery.rapidapi.host');
        //---------------------------------------------------------------------------------------------
        // Getting today's raffles without result which look_for_result_hour matches the current hour
        //---------------------------------------------------------------------------------------------
        $pendingRaffles = Raffle::where('raffle_date', $raffleDate)
                            ->where('look_for_result_hour', $currentHour)
                            ->where('result', '')
                            ->where('lottery_id', 1)
                            ->where('raffle_time', 'MIDDAY')
                            ->take(1)
                            ->get();
        if ($pendingRaffles->count() == 0) {
            $userMess = 'No raffles to process';
            info('No raffles to process');
            $response->indiOkey = false;
            $response->userMess = $userMess;
            $response->data = 'N/A';
            return $response;
        }
        $procRaff = [];
        $response->qtyProcessed = 0;
        $response->qtyErrors = 0;
        foreach ($pendingRaffles as $raffle) {
            $lotteryId = $raffle->lottery_id;
            $time      = $raffle->raffle_time;
            //------------------------------------------------
            // Getting the games which the raffle depends on
            //------------------------------------------------
            $dependence = RaffleTimesDependence::findByLotteryAndTime($lotteryId, $time);
            $games      = $dependence->lottery_games_ids;
            $updaResult = '';
            $references = [];
            foreach ($games as $game) {
                //-----------------------------------------------------------
                // Getting the URL of the game to call the rapidapi service
                //-----------------------------------------------------------
                $lotteryGame = LotteryGame::findByLotteryAndGame($lotteryId, $game);
                $apiUrl = $lotteryGame->api_url;
                //----------------------------
                // Call the rapidapi service
                //----------------------------
                try {
                    $apiResponse = Http::withHeaders([
                        'x-rapidapi-host' => $host,
                        'x-rapidapi-key' => $apiKey,
                    ])->get($apiUrl);
                    $result = $apiResponse->json();
                    if ($apiResponse['message'] === 'ok') {
                        //-------------------------------------
                        // Keeping the result in the database
                        //-------------------------------------
                        $drawResult = DrawingResult::updateOrCreate([
                            'lottery_game_id' => $lotteryGame->id,
                            'draw_number'     => $result['data']['drawNumber'],
                        ], [
                            'draw_date'       => $result['data']['drawDate'],
                            'draw_time'       => $result['data']['drawTime'],
                            'result'          => implode('', $result['data']['winningNumbers']),
                        ]);
                        $updaResult .= $drawResult->result;
                        $references[] = $drawResult->draw_number;
                    } else {
                        Log::error('Error calling the rapidapi service: '.$apiResponse['message']);
                        $response->indiOkey  = false;
                        $response->userMess .= $apiResponse['message'].'<br>';
                        $response->data      = $raffle->code;
                        $response->qtyErrors++;
                    }
                } catch (\Error $e) {
                    Log::error('Error keeping the result in the DB: '.$e->getMessage());
                    $response->indiOkey  = false;
                    $response->userMess .= $e->getMessage().'<br>';
                    $response->data      = $raffle->code;
                    $response->qtyErrors++;
                }
            }
            if ($response->qtyErrors == 0) {
                //-----------------------------
                // Updating the raffle result
                //-----------------------------
                try {
                    $raffle->update([
                        'result' => $updaResult,
                        'draw_references' => $references,
                        'result_registered_at' => now(),
                    ]);
                    $response->qtyProcessed++;
                    //-----------------------------------------------------------------------------------
                    // Keeping the raffle in an array to execute the Scrutiny process on the next step
                    //-----------------------------------------------------------------------------------
                    $procRaff[] = $raffle;
                } catch (\Error $e) {
                    Log::error('Error updating the raffle result');
                    $response->indiOkey  = false;
                    $response->userMess .= $e->getMessage().'<br>';
                    $response->data      = 'N/A';
                    $response->qtyErrors++;
                }
            }
        }
        if ($response->qtyErrors == 0) {
            $userMess = sprintf(
                'Qty Processed: %s<br>',
                $response->qtyProcessed
            );
        } else {
            $userMess = sprintf(
                'Qty Processed: %s<br>Qty Errors: %s<br>Errors Desc: %s',
                $response->qtyProcessed,
                $response->qtyErrors,
                $response->userMess
            );
        }
        $response->userMess = $userMess;
        $response->data = $procRaff;
        return $response;
    }

    public static function createRaffles(Collection $lotteries): PResponse
    {
        info('');
        info('LotteryServices::createRaffles');
        info('==============================');

        $response = new PResponse();
        $user     = User::findByEmail('batch@dreambet.ht');
        if ($user === null) {
            $response->indiOkey = false;
            $response->userMess = 'User not found';
            $response->qtyErrors++;
            Log::error('User not found');
            return $response;
        }

        $weekDays = [
            'Monday'    => 1,
            'Tuesday'   => 2,
            'Wednesday' => 3,
            'Thursday'  => 4,
            'Friday'    => 5,
            'Saturday'  => 6,
            'Sunday'    => 7,
        ];
        /** @var Lottery $lottery */
        foreach ($lotteries as $lottery) {
            info('Lottery: '.$lottery->name);
            //-------------------------------------------------
            // Getting the drawing days for the lottery games
            //-------------------------------------------------
            $lotteryGames   = $lottery->lotteryGames()->get();
            $drawingDays    = [];
            $data           = [];
            $firstIteration = true;
            foreach ($lotteryGames as $lotteryGame) {
                info('Game: '.$lotteryGame->name);
                $otherResponse = LotteryGameServices::getDrawingDays($lotteryGame);
                $data = $otherResponse->data;
                if ($firstIteration) {
                    $drawingDays = $data['drawDays'];
                    $firstIteration = false; // Set to false after the first iteration
                } else {
                    //--------------------------------------------------------------------------
                    // Get the intersection of the current drawing days with the previous ones
                    //--------------------------------------------------------------------------
                    $drawingDays = array_intersect($drawingDays, $data['drawDays']);
                }
            }
            if (empty($data)) {
                $response->indiOkey = false;
                $response->userMess = 'No drawing days found';
                $response->qtyErrors++;
                Log::error('No drawing days found');
                return $response;
            }
            $stopSaleTime = $data['stopSaleTime'];
            $drawHour     = $data['drawHour'];
            //---------------------------------------------------------------------------------
            // Now that we have the drawing days for all the games, we can create the raffles
            // for the next week, based on the drawing days
            //---------------------------------------------------------------------------------
            $rafflesCountBefore = Raffle::byLottery($lottery->id)->future()->count();
            info('Raffles already created: ' . $rafflesCountBefore);
            try {
                $raffles = [];
                $raffleTimes  = explode(',', $lottery->raffle_times);
                $date = Carbon::now()->subDay();
                info('Drawing Days: ' . implode(', ', $drawingDays));
                foreach ($drawingDays as $day) {
                    foreach ($raffleTimes as $raffleTime) {
                        $date->addDays($weekDays[$day]);
                        $dateFormatted = $date->format('Y-m-d');
                        $lookForResultHour = Carbon::parse($drawHour)->addMinutes(11)->format('H:i');
                        $raffles[] = [
                            'lottery_id'           => $lottery->id,
                            'raffle_date'          => $dateFormatted,
                            'raffle_time'          => $raffleTime,
                            'stop_sale_time'       => $stopSaleTime,
                            'draw_hour'            => $drawHour,
                            'look_for_result_hour' => $lookForResultHour,
                            'code'                 => RaffleServices::generateCode($lottery->code, $dateFormatted, $raffleTime),
                            'raffle_code'          => RaffleServices::generateRaffleCode($lottery->code,  $raffleTime),
                            'result'               => null,
                            'created_by'           => $user->id,
                            'created_at'           => Carbon::now(),
                            'updated_at'           => Carbon::now(),
                        ];
                    }
                }
                info('Raffles to create: ' . count($raffles));
                Raffle::insertOrIgnore($raffles);
                $rafflesCountAfter = Raffle::byLottery($lottery->id)->future()->count();
                $rafflesCountCreated = $rafflesCountAfter - $rafflesCountBefore;
                $response->qtyProcessed += $rafflesCountCreated;
                $userMess = '('.$rafflesCountCreated.') Raffles created successfully for Lottery: '.$lottery->name;
                $response->indiOkey = true;
                $response->userMess = $userMess;
                info($userMess);
            } catch (\Exception $e) {
                $response->indiOkey = false;
                $response->userMess = $e->getMessage();
                $response->qtyErrors++;
                Log::error($e->getMessage());
                return $response;
            }
        }
        $response->userMess = '('.$response->qtyProcessed.') Raffles created successfully';
        return $response;
    }

    public static function getDaysInNextMonth(): int
    {
        // Get the current date
        $currentDate = new DateTime();

        // Add one month to the current date
        $currentDate->add(new DateInterval('P1M'));

        // Get the first day of the next month
        $firstDayNextMonth = new DateTime($currentDate->format('Y-m-01'));

        // Get the last day of the next month
        $lastDayNextMonth = $firstDayNextMonth->format('t');

        return (int)$lastDayNextMonth;
    }

}
