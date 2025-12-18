<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $commission_id
 * @property string $raffle_time
 * @property int $game_id
 * @property int $commission_perc
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Commission $commission
 * @property-read \App\Models\Game $game
 * @method static \Database\Factories\CommissionDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereCommissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereCommissionPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommissionDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CommissionDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'commission_id',
        'raffle_time',
        'game_id',
        'commission_perc',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'commission_id' => 'integer',
        'game_id'       => 'integer',
        'data'          => 'json',
    ];


    protected function commissionPerc(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    // -----------------
    // Relationships
    // -----------------
    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
