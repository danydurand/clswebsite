<?php

namespace App\Domain\Question;

use App\Models\Event;
use App\Models\Option;
use App\Models\Category;
use App\Models\Question;
use App\Classes\PResponse;
use Illuminate\Support\Collection;
use App\Models\EquivalentOptionText;
use App\Models\UntranslatedQuestion;
use App\Models\EquivalentQuestionText;

class QuestionServices
{

    public static function equivalence(PResponse $response, Collection $equivalentQuestions, string $apiQuestionTitle): array
    {
        $questionTitle = $apiQuestionTitle;

        $equivalentQuestion = $equivalentQuestions->first(function ($eq) use ($apiQuestionTitle) {
            return $eq->api_question_title === $apiQuestionTitle;
        });
        if ($equivalentQuestion) {
            $questionTitle = $equivalentQuestion->real_question_title;
            info('Question equivalent found: '.$apiQuestionTitle);
        } else {
            info('No question equivalent found: '.$apiQuestionTitle);
            $response->qtyErrors++;
            $response->errors[] =  [
                'reference' => $questionTitle,
                'message'   => 'No equivalent found: '.$apiQuestionTitle,
                'comment'   => 'Looking for equivalent question title',
            ];
        }

        return [$response, $equivalentQuestion, $questionTitle];
    }

    public static function updateQuestionTitles(EquivalentQuestionText $equivalentQuestionText): PResponse
    {
        $response = new PResponse();

        $categoryEventIds = Event::open()
            ->where('category_id', $equivalentQuestionText->category_id)
            ->pluck('id');

        info('There are: '.count($categoryEventIds).' events to be processed');

        $updatedQuestions = Question::whereIn('event_id', $categoryEventIds)
            ->where('title', $equivalentQuestionText->api_question_title)
            ->update([
                'title' => $equivalentQuestionText->real_question_title,
                'was_translated' => true,
            ]);

        info($updatedQuestions.' questions were updated');

        $response->qtyProcessed = $updatedQuestions;

        return $response;
    }




}
