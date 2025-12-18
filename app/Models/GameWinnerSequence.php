<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $game_id
 * @property int $winning_factor
 * @property string $winner_position
 * @property int|null $position_order
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence byGame(int $gameId)
 * @method static \Database\Factories\GameWinnerSequenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence wherePositionOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence whereWinnerPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameWinnerSequence whereWinningFactor($value)
 * @mixin \Eloquent
 */
class GameWinnerSequence extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;

    protected $table = 'lottery.game_winner_sequences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id',
        'winning_factor',
        'winner_position',
        'position_order',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'game_id'        => 'integer',
        'position_order' => 'integer',
        'data'           => 'json',
    ];

    //----------
    // Finders 
    //----------


    //---------
    // Scopes 
    //---------
    public function scopeByGame($query, int $gameId)
    {
        return $query->where('game_id', $gameId);
    }


    //------------
    // Mutators
    //------------
    protected function setWinnerPositionAttribute($value)
    {
        $this->attributes['winner_position'] = substr($value,0,20);
    }


    //-------------
    // Attributes
    //-------------
    protected function winningFactor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    //----------------
    // Relationships 
    //----------------
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
