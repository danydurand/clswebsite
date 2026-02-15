<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawMethod extends Model
{
    use HasFactory;
    use HasRelatedRecords;
    use HandleActive;

    protected $fillable = [
        'name',
        'image',
        'min_limit',
        'max_limit',
        'fixed_charge',
        'percent_charge',
        'rate',
        'is_active',
        'form_id',
        'currency',
        'description',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
    ];


    //----------
    // Finders
    //----------
    public static function findByName(string $name): ?self
    {
        return self::where('name', strtoupper($name))->first();
    }


    //---------
    // Scopes
    //---------
    // No indexed fields in migration


    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = up($value, 100);
    }

    protected function setImageAttribute($value)
    {
        $this->attributes['image'] = $value ? length($value, 100) : null;
    }

    protected function setCurrencyAttribute($value)
    {
        $this->attributes['currency'] = up($value, 50);
    }

    protected function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $value ? up($value, 100) : null;
    }


    //-------------
    // Attributes
    //-------------
    protected function minLimit(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function maxLimit(): Attribute
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
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
