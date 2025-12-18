<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\AuthUser;
use App\Models\Traits\JsonData;
use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int|null $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $type
 * @property bool|null $is_active
 * @property string|null $remember_token
 * @property bool|null $can_cancel
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $mobile_verified_at
 * @property int|null $referrer_id
 * @property int|null $customer_id
 * @property int|null $consortium_id
 * @property int|null $banker_id
 * @property int|null $bank_id
 * @property int|null $restriction_id
 * @property string|null $restriction_assigned_at
 * @property int|null $commission_id
 * @property string|null $commission_assigned_at
 * @property int|null $payment_id
 * @property string|null $payment_assigned_at
 * @property int|null $limit_id
 * @property string|null $limit_assigned_at
 * @property int|null $failed_attempts
 * @property string|null $last_login_at
 * @property string|null $banned_reason
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Commission|null $commission
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read mixed $is_banker
 * @property-read \App\Models\Limit|null $limit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Login> $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Restriction|null $restriction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $todayTickets
 * @property-read int|null $today_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $weekTickets
 * @property-read int|null $week_tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller banned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller bySupervisor(int $supervisorId)
 * @method static \Database\Factories\SellerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereBankerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereBannedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereCanCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereCommissionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereCommissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereFailedAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereLimitAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereLimitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereMobileVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller wherePaymentAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereRestrictionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereRestrictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Seller whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bet> $bets
 * @property-read int|null $bets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bet> $todayBets
 * @property-read int|null $today_bets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bet> $weekBets
 * @property-read int|null $week_bets_count
 * @mixin \Eloquent
 */
class Seller extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory, Notifiable, JsonData, HandleActive, HasRelatedRecords;

    protected $table = 'lottery.seller_view';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'can_cancel',
        'consortium_id',
        'bank_id',
        'type',
        'failed_attempts',
        'last_login_at',
        'banned_reason',
        'restriction_id',
        'restriction_assigned_at',
        'commission_id',
        'commission_assigned_at',
        'payment_id',
        'payment_assigned_at',
        'limit_id',
        'limit_assigned_at',
        'data',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'consortium_id' => 'integer',
            'bank_id' => 'integer',
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'restriction_id' => 'integer',
            'commission_id' => 'integer',
            'payment_id' => 'integer',
            'limit_id' => 'integer',
            'data' => 'json',
        ];
    }

    //----------
    // Finders
    //----------
    public static function findByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setBannedReasonAttribute($value)
    {
        $this->attributes['banned_reason'] = strtoupper(substr($value, 0, 250));
    }

    //-----------
    // Scopes
    //-----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeBySupervisor($query, int $supervisorId)
    {
        $bankIds = Bank::bySupervisor($supervisorId)->pluck('id');
        return $query->whereIn('bank_id', $bankIds);
    }

    public function scopeByBank($query, int $bankId)
    {
        return $query->where('bank_id', $bankId);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_active', false);
    }


    //-------------
    // Attributes
    //-------------
    public function getIsBankerAttribute($value)
    {
        return $this->bank->is_banker;
    }

    //-----------------
    // Relationships
    //-----------------
    public function limit(): BelongsTo
    {
        return $this->belongsTo(Limit::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function restriction(): BelongsTo
    {
        return $this->belongsTo(Restriction::class);
    }

    public function logins(): MorphMany
    {
        return $this->morphMany(Login::class, 'entity');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function todayTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->whereDate('created_at', now()->format('Y-m-d'));
    }

    public function weekTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->whereDate('created_at', '>=', now()->startOfWeek()->toDateString());
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }

    public function todayBets(): HasMany
    {
        return $this->hasMany(Bet::class)
            ->whereDate('created_at', now()->format('Y-m-d'));
    }

    public function weekBets(): HasMany
    {
        return $this->hasMany(Bet::class)
            ->whereDate('created_at', '>=', now()->startOfWeek()->toDateString());
    }


}
