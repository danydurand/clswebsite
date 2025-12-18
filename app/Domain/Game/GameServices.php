<?php

namespace App\Domain\Game;

use App\Models\Game;

class GameServices
{

    public static function expl(): string
    {
        $games = Game::active()->get();
        $explanation = '';
        foreach ($games as $game) {
            if (strlen($explanation)) {
                $explanation .= ';';
            }
            $explanation .= trim($game->explanation);
        }
        return $explanation;
    }




}
