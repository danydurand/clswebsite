<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use App\Domain\Ticket\TicketStatusEnum;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Ticket\PaymentStatusEnum;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $stake_amount
 * @property int $prize_amount
 * @property int $commission
 * @property int $profit
 * @property TicketStatusEnum $status
 * @property string $code
 * @property int|null $consortium_id
 * @property int|null $supervisor_id
 * @property int|null $group_id
 * @property int|null $bank_id
 * @property int|null $seller_id
 * @property int|null $terminal_id
 * @property bool|null $won
 * @property int|null $payment_id
 * @property int|null $customer_id
 * @property PaymentStatusEnum|null $payment_status
 * @property string|null $phone
 * @property array<array-key, mixed>|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketAction> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Bank|null $bank
 * @property mixed $commision
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \App\Models\Customer|null $customer
 * @property-read mixed $physically_sold
 * @property-read mixed $security_code
 * @property-read \App\Models\Group|null $group
 * @property-read mixed $number
 * @property-read \App\Models\Payment|null $paymentProfile
 * @property-read mixed $qty_winner_bets
 * @property-read \App\Models\User|null $seller
 * @property-read \App\Models\User|null $supervisor
 * @property-read \App\Models\Terminal|null $terminal
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketDetail> $ticketDetails
 * @property-read int|null $ticket_details_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket byCustomer(int $customerId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket bySeller(int $sellerId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket bySupervisor(int $supervisorId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket byTerminal(int $terminalId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket cancelled()
 * @method static \Database\Factories\TicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket looser()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket notCancelled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket oneMonthBefore()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket physicalSale()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePrizeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereStakeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereTerminalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket winner()
 * @mixin \Eloquent
 */
class Ticket extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;
    use HasRelatedRecords;

    protected $table = 'lottery.tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'stake_amount',
        'payment_status',
        'status',
        'phone',
        'won',
        'prize_amount',
        'commission',
        'profit',
        'code',
        'data',
        'consortium_id',
        'supervisor_id',
        'group_id',
        'bank_id',
        'seller_id',
        'terminal_id',
        'payment_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'payment_status' => PaymentStatusEnum::class,
        'status' => TicketStatusEnum::class,
        'won' => 'boolean',
        'physically_sold' => 'boolean',
        'terminal_id' => 'integer',
        'consortium_id' => 'integer',
        'supervisor_id' => 'integer',
        'group_id' => 'integer',
        'seller_id' => 'integer',
        'bank_id' => 'integer',
        'payment_id' => 'integer',
        'data' => 'json',
    ];

    public $appends = [
        'number',
        'physically_sold',
        'qty_winner_bets',
    ];


    //----------
    // Finders
    //----------
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    //----------
    // Scopes
    //----------
    // public function scopePhysicalSale($query)
    // {
    //     return $query->whereNotNull('seller_id');
    // }

    public function scopePaid($query)
    {
        return $query->where('status', TicketStatusEnum::Paid->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', TicketStatusEnum::Pending->value);
    }

    public function scopeCancelled($query)
    {
        return $query->whereIn('status', [
            TicketStatusEnum::Cancelled->value,
        ]);
    }

    public function scopeNotCancelled($query)
    {
        return $query->whereNotIn('status', [
            TicketStatusEnum::Cancelled->value,
            TicketStatusEnum::AutoCancelled->value
        ]);
    }


    public function scopeOneMonthBefore($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth()->subMonth())
            ->where('created_at', '<=', now()->endOfMonth()->subMonth());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    public function scopeThisMonth($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth())
            ->where('created_at', '<=', now()->endOfMonth());
    }

    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeByBank($query, int $bankId)
    {
        return $query->where('bank_id', $bankId);
    }

    // public function scopeByTerminal($query, int $terminalId)
    // {
    //     return $query->where('terminal_id', $terminalId);
    // }

    // public function scopeBySupervisor($query, int $supervisorId)
    // {
    //     return $query->where('supervisor_id', $supervisorId);
    // }

    // public function scopeBySeller($query, int $sellerId)
    // {
    //     return $query->where('seller_id', $sellerId);
    // }

    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }


    public function scopeWinner($query)
    {
        return $query->where('won', true)
            ->notCancelled();
    }

    public function scopeLooser($query)
    {
        return $query->where('won', false);
    }

    // public function scopeByScrutiny($query, int $scrutinyId)
    // {
    //     return $query->paid()->where('scrutiny_id', $scrutinyId);
    // }


    //-------------
    // Attributes
    //-------------
    public function getPhysicallySoldAttribute($value)
    {
        return $this->seller_id !== null;
    }

    public function getSecurityCodeAttribute($value)
    {
        $uuid = explode('-', $this->code);
        return $uuid[2];
    }


    protected function number(): Attribute
    {
        $id = str_pad($this->id, 8, '0', STR_PAD_LEFT);
        $bankId = str_pad($this->bank_id, 6, '0', STR_PAD_LEFT);
        $sellerId = str_pad($this->seller_id, 6, '0', STR_PAD_LEFT);
        $customerId = str_pad($this->customer_id, 8, '0', STR_PAD_LEFT);
        if ($this->seller_id) {
            //-------------------------
            // It was physically sold
            //-------------------------
            $number = $bankId . '-' . $sellerId . '-' . $id;
        } else {
            //-------------------------
            // It was sold online
            //-------------------------
            $number = $customerId . '-' . $id;
        }
        return Attribute::make(
            get: fn($value) => $number,
        );
    }

    protected function stakeAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function prizeAmount(): Attribute
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
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function qtyWinnerBets(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->ticketDetails()->where('won', true)->count()
        );
    }


    //----------------
    // Relationships
    //----------------
    public function actions(): HasMany
    {
        return $this->hasMany(TicketAction::class);
    }

    public function paymentProfile(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

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

    public function ticketDetails(): HasMany
    {
        return $this->hasMany(TicketDetail::class);
    }
}
