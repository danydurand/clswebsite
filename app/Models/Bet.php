<?php

namespace App\Models;

use App\Domain\Bet\BetTypeEnum;
use App\Domain\Bet\BetStatusEnum;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $stake_amount
 * @property int $return_amount
 * @property bool $amount_returned
 * @property BetTypeEnum $type
 * @property BetStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $result_time
 * @property int|null $consortium_id
 * @property string|null $bet_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BetDetail> $betDetails
 * @property-read int|null $bet_details_count
 * @property-read \App\Models\Customer $customer
 * @method static \Database\Factories\BetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet loser()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet refunded()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereAmountReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereBetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereResultTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereReturnAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereStakeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet winner()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet tomorrow()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet yesterday()
 * @property string $code
 * @property int|null $supervisor_id
 * @property int|null $group_id
 * @property int|null $bank_id
 * @property int|null $seller_id
 * @property int|null $terminal_id
 * @property string|null $nullify_reason
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BetAction> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Bank|null $bank
 * @property mixed $commission
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read mixed $physically_sold
 * @property-read \App\Models\Group|null $group
 * @property mixed $profit
 * @property-read mixed $qty_winner_details
 * @property-read \App\Models\User|null $seller
 * @property-read \App\Models\User|null $supervisor
 * @property-read \App\Models\Terminal|null $terminal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet byEvent(int $eventId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet bySupervisor(int $supervisorId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet physicalSale()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bet webSale()
 * @mixin \Eloquent
 */
class Bet extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;

    protected $table = 'sports.bets';

    protected $fillable = [
        'customer_id',
        'code',
        'consortium_id',
        'supervisor_id',
        'group_id',
        'bank_id',
        'seller_id',
        'terminal_id',
        'type',
        'status',
        'stake_amount',
        'return_amount',
        'commission',
        'profit',
        'amount_returned',
        'result_time',
        'bet_number',
        'nullify_reason',
        'won',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'customer_id' => 'integer',
            'consortium_id' => 'integer',
            'supervisor_id' => 'integer',
            'group_id' => 'integer',
            'bank_id' => 'integer',
            'seller_id' => 'integer',
            'terminal_id' => 'integer',
            'won' => 'boolean',
            'amount_returned' => 'boolean',
            'result_time' => 'datetime',
            'type' => BetTypeEnum::class,
            'status' => BetStatusEnum::class,
        ];
    }

    public $appends = [
        'physically_sold',
        'qty_winner_details',
    ];

    //-----------
    // Finders
    //-----------
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByBetNumber(string $betNumber): ?self
    {
        return self::where('bet_number', $betNumber)->first();
    }

    //----------
    // Scopes
    //----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('sports.bets.consortium_id', $consortiumId);
    }

    public function scopeBySupervisor($query, int $supervisorId)
    {
        return $query->where('sports.bets.supervisor_id', $supervisorId);
    }

    public function scopeWebSale($query)
    {
        return $query->whereNull('sports.bets.consortium_id');
    }

    public function scopePhysicalSale($query)
    {
        return $query->whereNotNull('sports.bets.consortium_id');
    }

    public function scopeWinner($query)
    {
        return $query->where('won', true);
    }

    public function scopeLoser($query)
    {
        return $query->where('won', false);
    }

    public function scopePending($query)
    {
        return $query->where('status', BetStatusEnum::Pending->value);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', BetStatusEnum::Refunded->value);
    }

    public function scopeNullified($query)
    {
        return $query->where('status', BetStatusEnum::Nullified->value);
    }

    public function scopeNotNullified($query)
    {
        return $query->where('status', '!=', BetStatusEnum::Nullified->value);
    }

    public function scopeYesterday($query)
    {
        return $query->whereHas('betDetails.question.event', function ($q) {
            $q->whereDate('start_time', today()->subDay()->toDateString());
        });
    }

    public function scopeToday($query)
    {
        return $query->whereHas('betDetails.question.event', function ($q) {
            $q->whereDate('start_time', today()->toDateString());
        });
    }

    public function scopeTomorrow($query)
    {
        return $query->whereHas('betDetails.question.event', function ($q) {
            $q->whereDate('start_time', today()->addDay()->toDateString());
        });
    }

    public function scopeByEvent($query, int $eventId)
    {
        return $query->whereHas('betDetails.question.event', function ($q) use ($eventId) {
            $q->where('id', $eventId);
        });
    }


    //-----------
    // Mutators
    //-----------
    protected function setNullifyReasonAttribute($value)
    {
        $this->attributes['nullify_reason'] = up($value, 200);
    }


    //--------------
    // Attributes
    //--------------
    protected function qtyWinnerDetails(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->betDetails()->where('status', BetStatusEnum::Win->value)->count()
        );
    }

    public function getPhysicallySoldAttribute($value)
    {
        return $this->consortium_id !== null;
    }

    protected function stakeAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function returnAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function commission(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function profit(): Attribute
    {
        return Attribute::make(
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(BetAction::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function betDetails(): HasMany
    {
        return $this->hasMany(BetDetail::class);
    }

}
