<?php

namespace App\Models;

use App\Domain\InvoicePaymentMethod\AvailableOnEnum;
use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property bool $is_active
 * @property bool $requires_reference
 * @property string $code
 * @property string $name
 * @property AvailableOnEnum $available_on
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoicePayment> $invoicePayments
 * @property-read int|null $invoice_payments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod active()
 * @method static \Database\Factories\InvoicePaymentMethodFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod physical()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod web()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereAvailableOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereRequiresReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentMethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoicePaymentMethod extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;
    use HandleActive;

    protected $fillable = [
        'name',
        'code',
        'requires_reference',
        'available_on',
        'is_active',
    ];

    protected $casts = [
        'id' => 'integer',
        'requires_reference' => 'boolean',
        'is_active' => 'boolean',
        'available_on' => AvailableOnEnum::class,
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
    // Scopes
    //-----------
    public function scopeWeb($query)
    {
        return $query->whereIn('available_on', [
            AvailableOnEnum::Web->value,
            AvailableOnEnum::Both->value,
        ]);
    }

    public function scopePhysical($query)
    {
        return $query->whereIn('available_on', [
            AvailableOnEnum::Physical->value,
            AvailableOnEnum::Both->value,
        ]);
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = up($value, 25);
    }

    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = up($value, 3);
    }

    //---------------
    // Relationships
    //---------------
    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

}
