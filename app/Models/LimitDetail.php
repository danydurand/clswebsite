<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $limit_id
 * @property string $raffle_time
 * @property int $game_id
 * @property int $max_amount
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @property-read \App\Models\Limit $limit
 * @method static \Database\Factories\LimitDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereLimitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereMaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LimitDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LimitDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'limit_id',
        'raffle_time',
        'game_id',
        'max_amount',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'limit_id' => 'integer',
        'game_id' => 'integer',
        'max_amount' => 'integer',
    ];


    //-------------
    // Attributes
    //-------------
    protected function maxAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    //----------------
    // Relationships
    //----------------
    public function limit(): BelongsTo
    {
        return $this->belongsTo(Limit::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
