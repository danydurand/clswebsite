<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $restriction_id
 * @property string $raffle_time
 * @property int $game_id
 * @property bool $is_active
 * @property int|null $max_bet_amount
 * @property \Illuminate\Support\Carbon|null $init_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $specific_numbers
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @property-read \App\Models\Restriction $restriction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail active()
 * @method static \Database\Factories\RestrictionDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail outdated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereInitDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereMaxBetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereRestrictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereSpecificNumbers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestrictionDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RestrictionDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'restriction_id',
        'raffle_time',
        'game_id',
        'max_bet_amount',
        'init_date',
        'is_active',
        'end_date',
        'specific_numbers',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'restriction_id' => 'integer',
        'game_id' => 'integer',
        'init_date' => 'date',
        'is_active' => 'boolean',
        'end_date' => 'date',
    ];


    //-----------
    // Scopes
    //-----------
    public function scopeOutdated($query)
    {
        return $query->where('end_date', '<=', now());
    }



    //-------------
    // Attributes
    //-------------
    protected function maxBetAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function restriction(): BelongsTo
    {
        return $this->belongsTo(Restriction::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
