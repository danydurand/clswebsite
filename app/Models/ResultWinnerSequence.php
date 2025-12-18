<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $raffle_id
 * @property int $game_id
 * @property string $sequence
 * @property int $winning_factor
 * @property int|null $position_order
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @property-read \App\Models\Raffle $raffle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence byRaffleAndGame(int $raffleId, int $gameId)
 * @method static \Database\Factories\ResultWinnerSequenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence wherePositionOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereRaffleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultWinnerSequence whereWinningFactor($value)
 * @mixin \Eloquent
 */
class ResultWinnerSequence extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;

    protected $table = 'lottery.result_winner_sequences';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'raffle_id',
        'game_id',
        'sequence',
        'winning_factor',
        'position_order',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'        => 'integer',
        'raffle_id' => 'integer',
        'game_id'   => 'integer',
        'data'      => 'json',
    ];


    //----------
    // Finders 
    //----------
    public static function findByRaffleAndGameAndSequence(int $raffleId, int $gameId, string $sequence): ?self
    {
        return self::where('raffle_id', $raffleId)
                    ->where('game_id', $gameId)
                    ->where('sequence', $sequence)
                    ->first();
    }

    //----------
    // Scopes 
    //----------
    public function scopeByRaffleAndGame($query, int $raffleId, int $gameId)
    {
        return $query->where('raffle_id', $raffleId)
                    ->where('game_id', $gameId)
                    ->orderBy('position_order');
    }
    



    //------------
    // Mutators
    //------------


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

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }
}
