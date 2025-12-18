<?php

namespace App\Models;

use App\Services\AuthUser;
use App\Models\Traits\JsonData;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Builder;
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
 * @property string|null $zone
 * @property int|null $supervisor_id
 * @property bool $is_banker
 * @property string|null $business_number
 * @property string|null $email
 * @property string|null $address
 * @property string|null $phone
 * @property int|null $restriction_id
 * @property string|null $restriction_assigned_at
 * @property int|null $commission_id
 * @property string|null $commission_assigned_at
 * @property int|null $payment_id
 * @property string|null $payment_assigned_at
 * @property int|null $limit_id
 * @property string|null $limit_assigned_at
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \App\Models\Commission|null $commission
 * @property-read \App\Models\Consortium $consortium
 * @property-read \App\Models\Limit|null $limit
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Restriction|null $restriction
 * @property-write mixed $contact_name
 * @property-read \App\Models\Supervisor|null $supervisor
 * @property-read \App\Models\Supervisor|null $supervisorByConsortium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Terminal> $terminals
 * @property-read int|null $terminals_count
 * @method static Builder<static>|Group active()
 * @method static Builder<static>|Group active()
 * @method static Builder<static>|Group byConsortium(int $consortiumId)
 * @method static Builder<static>|Group byRestriction(int $restrictionId)
 * @method static Builder<static>|Group bySupervisor(int $supervisorId)
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static Builder<static>|Group inactive()
 * @method static Builder<static>|Group inactive()
 * @method static Builder<static>|Group newModelQuery()
 * @method static Builder<static>|Group newQuery()
 * @method static Builder<static>|Group query()
 * @method static Builder<static>|Group whereAddress($value)
 * @method static Builder<static>|Group whereBusinessNumber($value)
 * @method static Builder<static>|Group whereCode($value)
 * @method static Builder<static>|Group whereCommissionAssignedAt($value)
 * @method static Builder<static>|Group whereCommissionId($value)
 * @method static Builder<static>|Group whereConsortiumId($value)
 * @method static Builder<static>|Group whereCreatedAt($value)
 * @method static Builder<static>|Group whereData($value)
 * @method static Builder<static>|Group whereEmail($value)
 * @method static Builder<static>|Group whereId($value)
 * @method static Builder<static>|Group whereIsActive($value)
 * @method static Builder<static>|Group whereIsBanker($value)
 * @method static Builder<static>|Group whereLimitAssignedAt($value)
 * @method static Builder<static>|Group whereLimitId($value)
 * @method static Builder<static>|Group whereName($value)
 * @method static Builder<static>|Group wherePaymentAssignedAt($value)
 * @method static Builder<static>|Group wherePaymentId($value)
 * @method static Builder<static>|Group wherePhone($value)
 * @method static Builder<static>|Group whereRestrictionAssignedAt($value)
 * @method static Builder<static>|Group whereRestrictionId($value)
 * @method static Builder<static>|Group whereSupervisorId($value)
 * @method static Builder<static>|Group whereUpdatedAt($value)
 * @method static Builder<static>|Group whereZone($value)
 * @mixin \Eloquent
 */
class Group extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, JsonData, HandleActive;
    use HasRelatedRecords;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'supervisor_id',
        'is_active',
        'code',
        'name',
        'zone',
        'is_banker',
        'business_number',
        'address',
        'phone',
        'contact_name',
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
        'supervisor_id' => 'integer',
        'restriction_id' => 'integer',
        'commission_id' => 'integer',
        'payment_id' => 'integer',
        'limit_id' => 'integer',
        'is_active' => 'boolean',
        'is_banker' => 'boolean',
    ];


    protected static function booted()
    {
        static::addGlobalScope('groups', function (Builder $builder) {
            // Only show groups that are not bankers
            $builder->where('is_banker', false);
        });
    }

    //----------
    // Finders
    //----------
    public static function findConsortiumAndName(int $consortiumId, string $name): ?self
    {
        return self::where('consortium_id', $consortiumId)
            ->where('name', $name)
            ->first();
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }


    //---------
    // Scopes
    //---------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeBySupervisor($query, int $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
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

    protected function setZoneAttribute($value)
    {
        $this->attributes['zone'] = strtoupper(substr($value, 0, 100));
    }

    protected function setBusinessNumberAttribute($value)
    {
        $this->attributes['business_number'] = strtoupper(substr($value, 0, 25));
    }

    protected function setContactNameAttribute($value)
    {
        $this->attributes['contact_name'] = strtoupper(substr($value, 0, 100));
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

    public function supervisorByConsortium(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }

    public function terminals(): HasManyThrough
    {
        return $this->hasManyThrough(Terminal::class, Bank::class);
    }
}
