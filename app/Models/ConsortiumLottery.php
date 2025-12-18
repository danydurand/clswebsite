<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $consortium_id
 * @property int $lottery_id
 * @property string $raffle_time
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Consortium $consortium
 * @property-read \App\Models\Lottery $lottery
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery byConsortium(int $consortiumId)
 * @method static \Database\Factories\ConsortiumLotteryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery whereLotteryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsortiumLottery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ConsortiumLottery extends Model implements \OwenIt\Auditing\Contracts\Auditable
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
        'consortium_id',
        'lottery_id',
        'raffle_time',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'consortium_id' => 'integer',
        'lottery_id' => 'integer',
        'is_active' => 'boolean',
    ];


    //----------
    // Finders
    //----------
    public static function findByConsortiumLotteryAndRaffleTime(int $consortiumId, int $lotteryId, string $raffleTime)
    {
        return self::where('consortium_id', $consortiumId)
            ->where('lottery_id', $lotteryId)
            ->where('raffle_time', $raffleTime)
            ->first();
    }

    //----------
    // Scopes
    //----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    //----------------
    // Relationships
    //----------------

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }
}
