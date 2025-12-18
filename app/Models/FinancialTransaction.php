<?php

namespace App\Models;

use App\Domain\FinancialTransaction\TrxTypeEnum;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $amount
 * @property int $charge
 * @property int $post_balance
 * @property TrxTypeEnum $trx_type
 * @property string $trx
 * @property string $remark
 * @property string|null $detail
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction byCustomer($customerId)
 * @method static \Database\Factories\FinancialTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FinancialTransaction query()
 * @mixin \Eloquent
 */
class FinancialTransaction extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;


    protected $fillable = [
        'customer_id',
        'amount',
        'charge',
        'post_balance',
        'trx_type',
        'trx',
        'remark',
        'detail',
        'data',
    ];


    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'customer_id' => 'integer',
            'trx_type'    => TrxTypeEnum::class,
            'data'        => 'array',
        ];
    }



    //----------
    // Finders
    //----------
    public function findByCustomerAmountTypeTrx(int $customerId, int $amount, string $type, string $trx)
    {
        return self::where('customer_id', $customerId)
                ->where('amount', $amount)
                ->where('trx_type', $type)
                ->where('trx', $trx)
                ->first();
    }

    public function findByCustomerChargeTypeTrx(int $customerId, int $charge, string $type, string $trx)
    {
        return self::where('customer_id', $customerId)
                ->where('charge', $charge)
                ->where('trx_type', $type)
                ->where('trx', $trx)
                ->first();
    }



    //---------
    // Scopes
    //---------
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }


    //-----------
    // Mutators
    //-----------
    protected function setRemarkAttribute($value)
    {
        $this->attributes['remark'] = up($value, 100);
    }

    protected function setDetailAttribute($value)
    {
        $this->attributes['detail'] = up($value, 250);
    }


    //-------------
    // Attributes
    //-------------
    protected function amount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function charge(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function postBalance(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

}
