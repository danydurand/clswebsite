<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// #[ObservedBy(RaffleObserver::class)]
/**
 * @property int $id
 * @property int $lottery_id
 * @property bool $is_available
 * @property Carbon $raffle_date
 * @property string $raffle_time
 * @property string $stop_sale_time
 * @property string $draw_hour
 * @property string $look_for_result_hour
 * @property string $code
 * @property string|null $raffle_code
 * @property string|null $result
 * @property Carbon|null $result_registered_at
 * @property array $draw_references
 * @property int|null $created_by
 * @property array<array-key, mixed>|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read int $qty_tickets
 * @property-read int $winner_tickets
 * @property-read \App\Models\Lottery $lottery
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResultWinnerSequence> $resultWinnerSequences
 * @property-read int|null $result_winner_sequences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle byLottery(int $lotteryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle creator(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle date(string $date)
 * @method static \Database\Factories\RaffleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle future()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle unavailable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereDrawHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereDrawReferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereLookForResultHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereLotteryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereRaffleCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereRaffleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereResultRegisteredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereStopSaleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Raffle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Raffle extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;
    use JsonData;

    protected $table = 'lottery.raffles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lottery_id',
        'is_available',
        'raffle_date',
        'raffle_time',
        'stop_sale_time',
        'draw_hour',
        'look_for_result_hour',
        'code',
        'raffle_code',
        'result',
        'result_registered_at',
        'draw_references',
        'created_by',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                   => 'integer',
        'lottery_id'           => 'integer',
        'is_available'         => 'boolean',
        'raffle_date'          => 'date',
        'result_registered_at' => 'date',
        'created_by'           => 'integer',
        'data'                 => 'json',
    ];

    public $appends = [
        'qty_tickets',
        'winner_tickets',
    ];

    //----------
    // Finders
    //----------
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByLotteryDateTime(int $lotteryId, string $date, string $time): ?self
    {
        return self::where('lottery_id', $lotteryId)
                    ->where('raffle_date', $date)
                    ->where('raffle_time', $time)
                    ->first();
    }


    //----------
    // Scopes
    //----------
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    public function scopeFuture($query)
    {
        $today = Carbon::now();
        return $query->where('raffle_date', '>', $today);
    }

    public function scopePending($query)
    {
        return $query->where('result','');
    }

    public function scopeByLottery($query, int $lotteryId)
    {
        return $query->where('lottery_id', $lotteryId);
    }

    public function scopeDate($query, string $date)
    {
        return $query->where('raffle_date', $date);
    }

    public function scopeCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }



    //------------
    // Mutators
    //------------
    /**
     * Get the draw_references attribute as an array.
     *
     * @param  string  $value
     * @return array
     */
    public function getDrawReferencesAttribute($value)
    {
        return explode(', ', $value);
    }

    /**
     * Set the draw_references attribute as a comma-separated string.
     *
     * @param  array  $value
     * @return void
     */
    public function setDrawReferencesAttribute($value)
    {
        $this->attributes['draw_references'] = implode(', ', $value);
    }

    protected function setRaffleTimeAttribute($value)
    {
        $this->attributes['raffle_time'] = strtoupper(substr($value,0,20));
    }

    protected function setResultAttribute($value)
    {
        $this->attributes['result'] = strtoupper(substr($value,0,20));
    }

    //-------------
    // Attributes
    //-------------
    public function getWinnerTicketsAttribute(): int
    {
        return Ticket::whereHas('ticketDetails', function ($query) {
            $query->where('raffle_id', $this->id)
                ->where('won', true);
        })
        ->count();
    }

    public function getQtyTicketsAttribute(): int
    {
        return $this->tickets()->count();
    }


    //----------------
    // Relationships
    //----------------
    public function tickets(): HasManyThrough
    {
        return $this->hasManyThrough(Ticket::class, TicketDetail::class, 'raffle_id', 'id', 'id', 'ticket_id');
    }

    public function resultWinnerSequences(): HasMany
    {
        return $this->hasMany(ResultWinnerSequence::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
