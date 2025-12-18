<?php

namespace App\Domain\ApiOdds;

use App\Models\EquivalentOptionText;
use App\Models\EquivalentQuestionText;
use App\Models\Event;
use App\Models\League;
use App\Models\ApiEvent;
use App\Models\Category;
use App\Classes\PResponse;
use App\Models\Participant;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Domain\Event\EventStatusEnum;
use App\Domain\League\LeagueServices;
use Illuminate\Support\Facades\Storage;

class ApiOddsServices
{

    public static function translateQuestionsAndOptions(PResponse $response, ?Question $question): PResponse
    {

        if ($question) {
            $untranslatedQuestions = collect([$question]);
        } else {
            $untranslatedQuestions = Question::untranslated()->get();
        }

        // info('There are: '.count($untranslatedQuestions) . ' pending to translate questions');

        foreach ($untranslatedQuestions as $uQuestion) {

            $title    = Str::of($uQuestion->title)->trim();
            $category = $uQuestion->event->category;
            info('Question: '.$title.'. Category: '.$category->slug);
            $eQuestion = EquivalentQuestionText::findByCategoryAndTitle($category->id, $title);
            if ($eQuestion instanceof EquivalentQuestionText) {
                info('Translation found');
                $uQuestion->update([
                    'title'          => $eQuestion->real_question_title,
                    'was_translated' => true
                ]);
                //--------------------------------------------------------------------
                // Once the question has been translated, we also update the options
                //--------------------------------------------------------------------
                $uQuestion->options->each(function ($option) use ($eQuestion, $response, $uQuestion)  {
                    info('Updating options for question: '.$eQuestion->real_question_title.'('.$eQuestion->id.')');
                    info('Option: '.$option->name);
                    $equivalentOptionText = EquivalentOptionText::findByQuestionAndName($eQuestion->id, $option->name);
                    if ($equivalentOptionText instanceof EquivalentOptionText) {
                        info('Translation found for option: '.$option->name);
                        $translation = $equivalentOptionText->real_option_name;
                        $response->qtyProcessed++;
                    } else {
                        info('No translation found for option: '.$option->name);
                        $translation = null;
                        $response->qtyErrors++;
                        $response->errors[] =  [
                            'reference' => 'Untranslated Question: '.$uQuestion->title,
                            'message'   => 'No translation found for option: '.$option->name,
                            'comment'   => 'Translating the option name',
                        ];
                    }
                    $option->update([
                        'name'           => $translation ? $translation : $option->name,
                        'was_translated' => true
                    ]);
                });
            } else {
                info('No translation found');
                $response->qtyErrors++;
                $response->errors[] =  [
                    'reference' => 'Untranslated Question: '.$uQuestion->title,
                    'message'   => 'No translation found for question: '.$title,
                    'comment'   => 'Translating the question title',
                ];
            }
        }
        return $response;
    }

}
