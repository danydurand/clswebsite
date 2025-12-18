<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use App\Models\Traits\JsonData;
use App\Services\AuthUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $bank_id
 * @property int $brand_id
 * @property bool $is_active
 * @property string $code
 * @property string $token
 * @property string $serial
 * @property int|null $restriction_id
 * @property string|null $restriction_assigned_at
 * @property int|null $commission_id
 * @property string|null $commission_assigned_at
 * @property int|null $payment_id
 * @property string|null $payment_assigned_at
 * @property int|null $limit_id
 * @property string|null $limit_assigned_at
 * @property string|null $model
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank $bank
 * @property-read \App\Models\Brand $brand
 * @property-read \App\Models\Commission|null $commision
 * @property-read \App\Models\Limit|null $limit
 * @property-read \App\Models\Payment|null $payment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal byBrand(int $brandId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal byModel(string $model)
 * @method static \Database\Factories\TerminalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereCommissionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereCommissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereLimitAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereLimitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal wherePaymentAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereRestrictionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereRestrictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Terminal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Terminal extends Model implements \OwenIt\Auditing\Contracts\Auditable
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
        'bank_id',
        'brand_id',
        'is_active',
        'code',
        'token',
        'serial',
        'model',
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
        'bank_id' => 'integer',
        'brand_id' => 'integer',
        'restriction_id' => 'integer',
        'commission_id' => 'integer',
        'payment_id' => 'integer',
        'limit_id' => 'integer',
        'is_active' => 'boolean',
    ];


    //----------
    // Finders
    //----------
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)
            ->first();
    }

    public static function findBySerial(string $serial): ?self
    {
        return self::where('serial', $serial)
            ->first();
    }

    public static function findByToken(string $token): ?self
    {
        return self::where('token', $token)
            ->first();
    }



    //---------
    // Scopes
    //---------
    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    public function scopeByBank($query, int $bankId)
    {
        return $query->where('bank_id', $bankId);
    }

    public function scopeByBrand($query, int $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeByConsortium($query, int $consortiumId)
    {
        $consortiumId = auth()->user()->consortium_id ?? 1;
        $bankIds = Bank::where('consortium_id', $consortiumId)->pluck('id');
        return $query->whereIn('bank_id', $bankIds);
    }


    //-----------
    // Mutators
    //-----------
    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(substr($value, 0, 10));
    }

    protected function setTokenAttribute($value)
    {
        $this->attributes['token'] = strtoupper(substr($value, 0, 30));
    }

    protected function setSerialAttribute($value)
    {
        $this->attributes['serial'] = substr($value, 0, 50);
    }

    protected function setModelAttribute($value)
    {
        $this->attributes['model'] = strtoupper(substr($value, 0, 50));
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

    public function commision(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
