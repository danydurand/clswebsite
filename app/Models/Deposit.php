<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Deposit\DepositStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'gateway_id',
        'amount',
        'charge',
        'rate',
        'final_amount',
        'detail',
        'payment_try',
        'trx',
        'deposit_date',
        'status',
        'from_api',
        'admin_feedback',
        'success_url',
        'failed_url',
        'btc_amount',
        'btc_wallet',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'gateway_id' => 'integer',
        'amount' => 'integer',
        'charge' => 'integer',
        'rate' => 'integer',
        'final_amount' => 'integer',
        'detail' => 'array',
        'payment_try' => 'integer',
        'deposit_date' => 'datetime',
        'from_api' => 'integer',
        'btc_amount' => 'integer',
        'status' => DepositStatusEnum::class,
    ];


    //----------
    // Finders
    //----------
    public static function findByTrx(string $trx): ?self
    {
        return self::where('trx', $trx)->first();
    }


    //---------
    // Scopes
    //---------
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByGateway($query, int $gatewayId)
    {
        return $query->where('gateway_id', $gatewayId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }


    //-----------
    // Mutators
    //-----------
    protected function amount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function charge(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function rate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function finalAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function btcAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function setTrxAttribute($value)
    {
        $this->attributes['trx'] = length($value, 40);
    }

    protected function setAdminFeedbackAttribute($value)
    {
        $this->attributes['admin_feedback'] = $value ? up($value, 250) : null;
    }

    protected function setSuccessUrlAttribute($value)
    {
        $this->attributes['success_url'] = $value ? length($value, 250) : null;
    }

    protected function setFailedUrlAttribute($value)
    {
        $this->attributes['failed_url'] = $value ? length($value, 250) : null;
    }

    protected function setBtcWalletAttribute($value)
    {
        $this->attributes['btc_wallet'] = $value ? length($value, 250) : null;
    }


    //-------------
    // Attributes
    //-------------


    //-----------------
    // Relationships
    //-----------------
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}
