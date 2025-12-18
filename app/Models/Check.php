<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $type
 * @property int $qty_checked_events
 * @property int $qty_bets
 * @property int $qty_winners
 * @property int $qty_losers
 * @property int $total_stake_amount
 * @property int $total_return_amount
 * @property int $profit
 * @property int|null $consortium_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read mixed $physically_sold
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Check byConsortium(int $consortiumId)
 * @method static \Database\Factories\CheckFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Check newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Check newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Check query()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @mixin \Eloquent
 */
class Check extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'sports.checks';


    protected $fillable = [
        'type',
        'qty_checked_events',
        'qty_bets',
        'qty_winners',
        'qty_losers',
        'total_stake_amount',
        'total_return_amount',
        'profit',
        'consortium_id',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'consortium_id' => 'integer',
        ];
    }


    //----------
    // Scopes
    //----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }


    //--------------
    // Attributes
    //--------------
    public function getPhysicallySoldAttribute($value)
    {
        return $this->consortium_id !== null;
    }

    protected function totalStakeAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function totalReturnAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function profit(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

}
