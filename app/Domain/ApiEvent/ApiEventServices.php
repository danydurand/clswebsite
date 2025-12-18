<?php

namespace App\Domain\ApiEvent;

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
use App\Domain\StatusCode\StatusCodeServices;

class ApiEventServices
{

    public static function updateRealEvents(PResponse $response): PResponse
    {

        info('');
        info('Updating Real Events...');
        info('');

        $apiEvents = ApiEvent::mustBeUpdated()->get();

        info('There are: ' . count($apiEvents) . ' pending events to be updated');

        $finishedStatusCodes = StatusCode::meansFinished()->pluck('id')->toArray();

        foreach ($apiEvents as $apiEvent) {

            info('');
            info('Processing ApiEvent Id: ' . $apiEvent->id . ' Real Event Id: ' . $apiEvent->event_id);
            info('');

            $event = Event::find($apiEvent->event_id);
            if ($event) {

                $statusCode = StatusCodeServices::getStatusCode($apiEvent);

                $event->status_code_id = $statusCode->id;
                $event->home_score = $apiEvent->home_score;
                $event->away_score = $apiEvent->away_score;
                $event->changes = $apiEvent->changes;
                $event->changes_time = Carbon::createFromTimestamp($apiEvent->changes_time);
                $event->save();

                $response->qtyProcessed++;
                //-------------------------------------------------------
                // Here we set the final state if the event is finished
                //-------------------------------------------------------
                if (in_array($event->status_code_id, $finishedStatusCodes)) {
                    EventServices::setFinalState($response, $event);
                }

                $apiEvent->must_be_updated = false;
                $apiEvent->was_updated_at = now();
                $apiEvent->updating_message = 'OK';
                $apiEvent->save();
                info('Api event updated: ' . $apiEvent->id);
            } else {
                Log::error('The real event with id ' . $apiEvent->event_id . ' does not exist');
                $response->qtyErrors++;
                $response->errors[] = [
                    'reference' => 'Event Id: ' . $apiEvent->event_id,
                    'message' => 'Updating real event',
                    'comment' => 'Real Event does not exist',
                ];
            }
            info('-------------------------------------');
        }
        return $response;
    }


    public static function convertToRealEvent(PResponse $response): PResponse
    {
        info('');
        info('Converting pending API Events to real Events...');
        info('');

        $pendingEvents = ApiEvent::pendingToConvert()->get();

        info('There are: ' . count($pendingEvents) . ' pending events');
        info('');

        foreach ($pendingEvents as $pEvent) {

            info('Event: ' . $pEvent->id . ' | ' . $pEvent->slug);
            $categorySlug = $pEvent->category_slug;
            info('Processing Category: ' . $categorySlug);
            $category = Category::findBySlug($categorySlug);
            //-----------------------------------------
            // Checking the existence of the League
            //-----------------------------------------
            $league = League::findBySlug($pEvent->league_slug);
            if (!($league instanceof League)) {
                info('Creating a new League: ' . $pEvent->league_slug);
                $league = League::create([
                    'category_id' => $category->id,
                    'is_active' => true,
                    'name' => $pEvent->league_name,
                    'short_name' => LeagueServices::getShortName($pEvent->league_slug, $category->id),
                    'slug' => $pEvent->league_slug,
                    'api_id' => $pEvent->league_id,
                ]);
            }
            //--------------------------------------------------
            // Checking the existence of the Home Participant
            //--------------------------------------------------
            info('Home Team: ' . $pEvent->home_team_id);
            $homeTeamId = $pEvent->home_team_id;
            $homeParticipant = Participant::findByApiId($homeTeamId);
            if (!($homeParticipant instanceof Participant)) {
                $homeParticipant = self::createParticipant($pEvent, 'home', $league);
            }
            //--------------------------------------------------
            // Checking the existence of the Away Participant
            //--------------------------------------------------
            $awayTeamId = $pEvent->away_team_id;
            $awayParticipant = Participant::findByApiId($awayTeamId);
            if (!($awayParticipant instanceof Participant)) {
                $awayParticipant = self::createParticipant($pEvent, 'away', $league);
            }
            //---------------------
            // Creating the Event
            //---------------------
            $startTime = Carbon::createFromDate($pEvent->start_time);
            $betStartTime = $startTime->clone()->subDay();
            $betEndTime = $startTime;

            $statusCode = StatusCodeServices::getStatusCode($pEvent);

            //--------------------------------------------------
            // Checking the existence of the Away Participant
            //--------------------------------------------------
            $exists = Event::findByHomeAwayStart($homeParticipant->id, $awayParticipant->id, $startTime);
            if ($exists instanceof Event) {
                info('The Event already exists: ' . $exists->slug);
            } else {
                $event = Event::create([
                    'category_id' => $category->id,
                    'home_participant_id' => $homeParticipant->id,
                    'away_participant_id' => $awayParticipant->id,
                    'slug' => $pEvent->slug . '-' . $pEvent->api_event_id,
                    'start_time' => $startTime,
                    'bet_start_time' => $betStartTime,
                    'bet_end_time' => $betEndTime,
                    'api_id' => $pEvent->api_event_id,
                    'status_code_id' => $statusCode->id,
                ]);

                $pEvent->event_id = $event->id;
                $pEvent->converted_at = now();
                $pEvent->conversion_message = 'OK';
                $pEvent->has_conversion_error = false;

                $response->qtyProcessed++;
            }
            $pEvent->save();
            info('-------------------------------------');
            info('');
        }

        return $response;
    }


    public static function createParticipant(ApiEvent $pEvent, string $type, League $league)
    {
        if ($type == 'home') {
            info('Creating a new home participant: ' . $pEvent->home_team_slug);
            $participant = Participant::create([
                'name' => $pEvent->home_team_name,
                'slug' => $pEvent->home_team_slug,
                'short_name' => $pEvent->home_team_name_code . $pEvent->home_team_id,
                'api_id' => $pEvent->home_team_id,
                'league_id' => $league->id,
            ]);
        } else {
            info('Creating a new away participant: ' . $pEvent->away_team_slug);
            $participant = Participant::create([
                'name' => $pEvent->away_team_name,
                'slug' => $pEvent->away_team_slug,
                'short_name' => $pEvent->away_team_name_code . $pEvent->away_team_id,
                'api_id' => $pEvent->away_team_id,
                'league_id' => $league->id,
            ]);
        }
        self::getParticipantImage($participant);
        return $participant;
    }

    public static function getParticipantImage(Participant $participant): PResponse
    {
        $response = new PResponse();

        info('');
        info('Getting participant image for: ' . $participant->name);

        $host = config('sport.rapidapi.host');
        $apiKey = config('sport.rapidapi.api-key');
        $endpoint = config('sport.endpoint.participant-image');
        $endpoint = Str::of($endpoint)
            ->replace('{id}', $participant->api_id);
        $apiUrl = 'https://' . $endpoint;

        info('apiURL: ' . $apiUrl);

        try {
            $apiResponse = Http::withHeaders([
                'x-rapidapi-host' => $host,
                'x-rapidapi-key' => $apiKey,
            ])->withOptions([
                        'verify' => true,
                    ])->get($apiUrl);

            if ($apiResponse->successful()) {
                $imageContents = $apiResponse->body();
                $imageName = $participant->slug . '.png';
                $path = "images/participants/$imageName";
                info("Image path: $path");
                Storage::disk('public')->put($path, $imageContents);
                $participant->image = $path;
                $participant->save();

                $response->userMess = 'Participant image updated successfully.';
                $response->qtyProcessed++;
            } else {
                Log::error('Error fetching participant image: ' . $apiResponse->body());
                $response->userMess = 'Error fetching participant image: ' . $apiResponse->body();
                $response->qtyErrors++;
                $response->errors[] = [
                    'reference' => $participant->slug,
                    'message' => 'Fetching participant image error',
                    'comment' => $apiResponse->body(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching participant image: ' . $e->getMessage());
            $response->userMess = 'Error fetching participant image: ' . $e->getMessage();
            $response->qtyErrors++;
            $response->errors[] = [
                'reference' => $participant->slug,
                'message' => 'Fetching participant image error',
                'comment' => $e->getMessage(),
            ];
        }

        return $response;
    }

    public static function getLeagueImage(League $league): PResponse
    {
        $response = new PResponse();

        info('');
        info('Getting league image for: ' . $league->name);

        $host = config('sport.rapidapi.host');
        $apiKey = config('sport.rapidapi.api-key');
        $endpoint = config('sport.endpoint.league-image');
        $endpoint = Str::of($endpoint)
            ->replace('{id}', $league->api_id);
        $apiUrl = 'https://' . $endpoint;

        info('apiURL: ' . $apiUrl);

        $apiResponse = Http::withHeaders([
            'x-rapidapi-host' => $host,
            'x-rapidapi-key' => $apiKey,
        ])->withOptions([
                    'verify' => true,
                ])->get($apiUrl);

        if ($apiResponse->successful()) {
            $imageContents = $apiResponse->body();
            $imageName = $league->slug . '.png';
            $path = "images/leagues/$imageName";
            info("Image path: $path");
            Storage::disk('public')->put($path, $imageContents);
            $league->image = $path;
            $league->save();
            $response->userMess = 'League image updated successfully.';
            $response->qtyProcessed++;
        } else {
            Log::error('Error fetching league image: ' . $apiResponse->body());
            $response->userMess = 'Error fetching league image: ' . $apiResponse->body();
            $response->qtyErrors++;
        }

        return $response;
    }


}
