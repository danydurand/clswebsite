<?php

namespace App\Models;

use App\Models\Game;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleSequence extends Model
{
    use \Sushi\Sushi;

    public function getRows()
    {
        $bor = Game::findByShortName('Bor');
        $mar = Game::findByShortName('Mar');
        $lt3 = Game::findByShortName('Lt3');
        $l3b = Game::findByShortName('L3b');
        $l41 = Game::findByShortName('L41');
        $l42 = Game::findByShortName('L42');
        $l43 = Game::findByShortName('L43');
        $l51 = Game::findByShortName('L51');
        $l52 = Game::findByShortName('L52');
        $l53 = Game::findByShortName('L53');

        $saleSequence   = [];
        $saleSequence[] = [
            'id'      => 1,
            'game_id' => $bor->id,
            'pick'    => 2,
            'char'    => null
        ];
        $saleSequence[] = [
            'id'      => 2,
            'game_id' => $mar->id,
            'pick'    => 4,
            'char'    => null
        ];
        $saleSequence[] = [
            'id'      => 3,
            'game_id' => $lt3->id,
            'pick'    => 3,
            'char'    => null
        ];
        $saleSequence[] = [
            'id'      => 4,
            'game_id' => $l3b->id,
            'pick'    => 3,
            'char'    => '*'
        ];
        $saleSequence[] = [
            'id'      => 5,
            'game_id' => $l41->id,
            'pick'    => 4,
            'char'    => '.1'
        ];
        $saleSequence[] = [
            'id'      => 6,
            'game_id' => $l42->id,
            'pick'    => 4,
            'char'    => '.2'
        ];
        $saleSequence[] = [
            'id'      => 7,
            'game_id' => $l43->id,
            'pick'    => 4,
            'char'    => '.3'
        ];
        $saleSequence[] = [
            'id'      => 8,
            'game_id' => $l41->id.','.$l42->id.','.$l43->id,
            'pick'    => 4,
            'char'    => '*'
        ];
        $saleSequence[] = [
            'id'      => 9,
            'game_id' => $l51->id,
            'pick'    => 5,
            'char'    => '.1'
        ];
        $saleSequence[] = [
            'id'      => 10,
            'game_id' => $l52->id,
            'pick'    => 5,
            'char'    => '.2'
        ];
        $saleSequence[] = [
            'id'      => 11,
            'game_id' => $l53->id,
            'pick'    => 5,
            'char'    => '.3'
        ];
        $saleSequence[] = [
            'id'      => 12,
            'game_id' => $l51->id.','.$l52->id.','.$l53->id,
            'pick'    => 5,
            'char'    => '*'
        ];
        $saleSequence[] = [
            'id'      => 13,
            'game_id' => $mar->id,
            'pick'    => 4,
            'char'    => '/'
        ];
        $saleSequence[] = [
            'id'      => 14,
            'game_id' => $l51->id,
            'pick'    => 5,
            'char'    => null
        ];

        return $saleSequence;
    }

    protected function sushiShouldCache()
    {
        return true;
    }

    //----------------
    // Relationship
    //----------------

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

}
