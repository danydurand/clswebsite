<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Login> $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Route> $routes
 * @property-read int|null $routes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor banned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor byConsortium(int $consortiumId)
 * @method static \Database\Factories\SupervisorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereBankerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereBannedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereCanCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereCommissionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereCommissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereFailedAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereLimitAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereLimitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereMobileVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor wherePaymentAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereRestrictionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereRestrictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supervisor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Supervisor extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory, Notifiable, JsonData, HandleActive, HasRelatedRecords;

    protected $table = 'lottery.supervisor_view';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'consortium_id',
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
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
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


    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_active', false);
    }


    //-----------------
    // Relationships
    //-----------------
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }

    public function logins(): MorphMany
    {
        return $this->morphMany(Login::class, 'entity');
    }

    public function consortium()
    {
        return $this->belongsTo(Consortium::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function banks(): HasManyThrough
    {
        return $this->hasManyThrough(Bank::class, Group::class);
    }

}
