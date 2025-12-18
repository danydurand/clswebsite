<?php

namespace App\Models;

use App\Services\AuthUser;
use App\Models\Traits\JsonData;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $consortium_id
 * @property bool $is_active
 * @property string $code
 * @property string $name
 * @property bool $is_banker
 * @property string $business_number
 * @property string $address
 * @property string $phone
 * @property int|null $restriction_id
 * @property string|null $restriction_assigned_at
 * @property int|null $commission_id
 * @property string|null $commission_assigned_at
 * @property int|null $payment_id
 * @property string|null $payment_assigned_at
 * @property int|null $limit_id
 * @property string|null $limit_assigned_at
 * @property array|null $data
 * @property string|null $email
 * @property string|null $zone
 * @property int|null $supervisor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \App\Models\Commission|null $commission
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \App\Models\Limit|null $limit
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Restriction|null $restriction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker byRestriction(int $restrictionId)
 * @method static \Database\Factories\BankerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereBusinessNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereCommissionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereCommissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereIsBanker($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereLimitAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereLimitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker wherePaymentAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereRestrictionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereRestrictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banker whereZone($value)
 * @mixin \Eloquent
 */
class Banker extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, JsonData, HandleActive;
    use HasRelatedRecords;

    protected $table = 'banker_view';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'is_active',
        'code',
        'name',
        'is_banker',
        'business_number',
        'email',
        'address',
        'phone',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'consortium_id' => 'integer',
        'restriction_id' => 'integer',
        'commission_id' => 'integer',
        'payment_id' => 'integer',
        'limit_id' => 'integer',
        'is_active' => 'boolean',
        'is_banker' => 'boolean',
    ];


    //----------
    // Finders
    //----------
    public static function findConsortiumAndName(int $consortiumId, string $name): ?self
    {
        return self::where('consortium_id', $consortiumId)
            ->where('name', $name)
            ->first();
    }

    public static function findConsortiumAndEmail(int $consortiumId, string $email): ?self
    {
        return self::where('consortium_id', $consortiumId)
            ->where('email', $email)
            ->first();
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByBusinessNumber(string $businessNumber): ?self
    {
        return self::where('business_number', $businessNumber)->first();
    }


    //---------
    // Scopes
    //---------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeByRestriction($query, int $restrictionId)
    {
        return $query->where('restriction_id', $restrictionId);
    }


    //-----------
    // Mutators
    //-----------
    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(substr($value, 0, 10));
    }

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setBusinessNumberAttribute($value)
    {
        $this->attributes['business_number'] = strtoupper(substr($value, 0, 25));
    }

    protected function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(substr($value, 0, 100));
    }

    protected function setAddressAttribute($value)
    {
        $this->attributes['address'] = strtoupper(substr($value, 0, 250));
    }

    protected function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = substr($value, 0, 25);
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

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class, 'group_id', 'id');
    }

    // public function terminals(): HasManyThrough
    // {
    //     return $this->hasManyThrough(Terminal::class, Bank::class);
    // }
}
