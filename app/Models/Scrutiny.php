<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $consortium_id
 * @property int $lottery_id
 * @property int $raffle_id
 * @property string $code
 * @property int $qty_winners
 * @property int $total_bet_amount
 * @property int $total_prize_amount
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Consortium $consortium
 * @property-read \App\Models\Lottery $lottery
 * @property-read \App\Models\Raffle $raffle
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny byLottery(int $lotteryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny byRaffle(int $raffleId)
 * @method static \Database\Factories\ScrutinyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereLotteryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereQtyWinners($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereRaffleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereTotalBetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereTotalPrizeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scrutiny whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Scrutiny extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;
    use HasRelatedRecords;

    protected $table = 'lottery.scrutinies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'lottery_id',
        'raffle_id',
        'code',
        'qty_winners',
        'total_bet_amount',
        'total_prize_amount',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'consortium_id' => 'integer',
        'lottery_id'    => 'integer',
        'raffle_id'     => 'integer',
        'data'          => 'json',
    ];


    public $appends = [
    ];

    //----------
    // Finders
    //----------
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByConsortiumAndRaffle(int $consortiumId, int $raffleId): ?self
    {
        return self::where('consortium_id', $consortiumId)
                    ->where('raffle_id', $raffleId)
                    ->first();
    }


    //----------
    // Scopes
    //----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        $query->where('consortium_id', $consortiumId);
    }

    public function scopeByLottery($query, int $lotteryId)
    {
        $query->where('lottery_id', $lotteryId);
    }

    public function scopeByRaffle($query, int $raffleId)
    {
        $query->where('raffle_id', $raffleId);
    }



    //------------
    // Mutators
    //------------
    protected function totalBetAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function totalPrizeAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function tickets(): HasManyThrough
    {
        return $this->hasManyThrough(Ticket::class, TicketDetail::class, 'scrutiny_id', 'id', 'id', 'ticket_id');
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }
}
