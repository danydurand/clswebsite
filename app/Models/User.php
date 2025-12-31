<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use App\Models\Traits\JsonData;
use App\Domain\User\UserTypeEnum;
use App\Models\Traits\HandleActive;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $type
 * @property bool $is_active
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
 * @property-read \App\Models\Banker|null $banker
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Login> $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $referees
 * @property-read int|null $referees_count
 * @property-read User|null $referrer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User banned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User customer()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User emailVerified()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User mobileVerified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User seller()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User supervisor()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User unverified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBannedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCanCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCommissionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCommissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFailedAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLimitAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLimitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobileVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePaymentAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRestrictionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRestrictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory, Notifiable, JsonData;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'customer_id',
        'referrer_id',
        'consortium_id',
        'banker_id',
        'bank_id',
        'type',
        'failed_attempts',
        'last_login_at',
        'banned_reason',
        'data',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'customer_id' => 'integer',
            'referrer_id' => 'integer',
            'consortium_id' => 'integer',
            'banker_id' => 'integer',
            'bank_id' => 'integer',
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'data' => 'json',
            // 'type'               => UserTypeEnum::class,
        ];
    }

    //----------
    // Finders
    //----------
    public static function findByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    //----------
    // Scopes
    //----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeByBank($query, int $bankId)
    {
        return $query->where('bank_id', $bankId);
    }

    public function scopeCustomer($query)
    {
        return $query->where('type', 'customer');
    }

    public function scopeSeller($query)
    {
        return $query->where('type', UserTypeEnum::Seller->value);
    }

    public function scopeSupervisor($query)
    {
        return $query->where('type', UserTypeEnum::Supervisor->value);
    }

    public function scopeBanned($query)
    {
        return $query->customer()->where('is_active', false);
    }

    public function scopeActive($query)
    {
        return $query->customer()->where('is_active', true)
            ->where(function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->orWhereNotNull('mobile_verified_at');
            });
    }

    public function scopeEmailVerified($query)
    {
        return $query->customer()->whereNotNull('email_verified_at');
    }

    public function scopeMobileVerified($query)
    {
        return $query->customer()->whereNotNull('mobile_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->customer()->whereNull('email_verified_at')
            ->whereNull('mobile_verified_at');
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = length($value, 100);
    }

    protected function setBannedReasonAttribute($value)
    {
        $this->attributes['banned_reason'] = strtoupper(substr($value, 0, 250));
    }


    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    //-----------------
    // Relationships
    //-----------------
    public function banker(): BelongsTo
    {
        return $this->belongsTo(Banker::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'seller_id');
    }

    public function logins(): MorphMany
    {
        return $this->morphMany(Login::class, 'entity');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function consortium()
    {
        return $this->belongsTo(Consortium::class);
    }

    // The user who referred this user
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    // The users referred by this user
    public function referees()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }
}
