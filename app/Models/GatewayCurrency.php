<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatewayCurrency extends Model
{
    use HasFactory;

    //-----------
    // Fillable
    //-----------
    protected $fillable = [
        'gateway_id',
        'name',
        'currency',
        'symbol',
        'min_amount',
        'max_amount',
        'fixed_charge',
        'percent_charge',
        'rate',
        'gateway_parameter',
    ];


    //-------
    // Casts
    //-------
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'gateway_id' => 'integer',
            'gateway_parameter' => 'array',
        ];
    }


    //---------
    // Scopes
    //---------
    public function scopeByGateway($query, int $gatewayId)
    {
        return $query->where('gateway_id', $gatewayId);
    }


    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = up($value, 255);
    }

    protected function setCurrencyAttribute($value)
    {
        $this->attributes['currency'] = up($value, 255);
    }

    protected function setSymbolAttribute($value)
    {
        $this->attributes['symbol'] = up($value, 255);
    }


    //-------------
    // Attributes
    //-------------
    protected function minAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function maxAmount(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function fixedCharge(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function percentCharge(): Attribute
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


    //-----------------
    // Relationships 
    //-----------------
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}
