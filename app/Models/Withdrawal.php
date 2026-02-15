<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'withdraw_method_id',
        'amount',
        'currency',
        'rate',
        'charge',
        'after_charge',
        'final_amount',
        'status',
        'trx',
        'withdraw_information',
        'admin_feedback',
    ];

    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'withdraw_method_id' => 'integer',
        'withdraw_information' => 'json',
    ];


    //----------
    // Finders
    //----------
    // No unique fields in migration


    //---------
    // Scopes
    //---------
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByWithdrawMethod($query, int $withdrawMethodId)
    {
        return $query->where('withdraw_method_id', $withdrawMethodId);
    }


    //-----------
    // Mutators
    //-----------
    protected function setCurrencyAttribute($value)
    {
        $this->attributes['currency'] = up($value, 50);
    }

    protected function setStatusAttribute($value)
    {
        $this->attributes['status'] = up($value, 25);
    }

    protected function setTrxAttribute($value)
    {
        $this->attributes['trx'] = length($value, 40);
    }

    protected function setAdminFeedbackAttribute($value)
    {
        $this->attributes['admin_feedback'] = up($value, 250);
    }


    //-------------
    // Attributes
    //-------------
    protected function amount(): Attribute
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

    protected function charge(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function afterCharge(): Attribute
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


    //-----------------
    // Relationships
    //-----------------
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function withdrawMethod(): BelongsTo
    {
        return $this->belongsTo(WithdrawMethod::class);
    }
}
