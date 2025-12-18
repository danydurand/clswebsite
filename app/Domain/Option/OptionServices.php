<?php

namespace App\Domain\Option;

use App\Models\Event;
use App\Models\Option;
use App\Models\Category;
use App\Models\Question;
use App\Classes\PResponse;
use Illuminate\Support\Collection;
use App\Models\EquivalentOptionText;
use App\Models\EquivalentQuestionText;

class OptionServices
{


    public static function equivalence(Collection $equivalentOptions, string $apiOptionName): string
    {
        $optionName = $apiOptionName;

        $equivalentOption = $equivalentOptions->first(function ($eo) use ($apiOptionName) {
            return $eo->api_option_name === $apiOptionName;
        });
        if ($equivalentOption) {
            $optionName = $equivalentOption->real_option_name;
            info('Option Translation found: '.$apiOptionName);
        } else {
            info('No option translation found: '.$apiOptionName);
        }

        return $optionName;
    }



    public static function updateOptionNames(EquivalentOptionText $equivalentOptionText): PResponse
    {
        $response = new PResponse();

        $equivalentQuestionText = $equivalentOptionText->equivalentQuestionText;

        $categoryEventIds = Event::open()
            ->where('category_id', $equivalentQuestionText->category_id)
            ->pluck('id');

        info('There are: '.count($categoryEventIds).' events to be processed');

        $questionIds = Question::whereIn('event_id', $categoryEventIds)->pluck('id');

        info('There are: '.count($questionIds).' questions to be processed');

        $updatedOptions = Option::whereIn('question_id', $questionIds)
            ->where('name', $equivalentOptionText->api_option_name)
            ->update(['name' => $equivalentOptionText->real_option_name]);

        info($updatedOptions.' options were updated');

        $response->qtyProcessed = $updatedOptions;

        return $response;
    }




}
