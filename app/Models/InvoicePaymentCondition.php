<?php

namespace App\Models;

use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $credit_days
 * @property bool $is_active
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Consortium> $consortiums
 * @property-read int|null $consortiums_count
 * @method static \Database\Factories\InvoicePaymentConditionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereCreditDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentCondition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoicePaymentCondition extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'credit_days',
        'is_active',
        'is_default',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];


    //----------
    // Finders
    //----------
    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }


    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value,0,50));
    }

    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = ucfirst(substr($value,0,3));
    }



    //----------------
    // Relationships
    //----------------
    // public function invoices(): HasMany
    // {
    //     return $this->hasMany(Invoice::class);
    // }

    public function consortiums(): HasMany
    {
        return $this->hasMany(Consortium::class);
    }
}
