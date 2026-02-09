<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gateway extends Model
{
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;

    //-------------
    // Fillable
    //-------------
    protected $fillable = [
        'code',
        'name',
        'alias',
        'is_active',
        'image',
        'is_crypto',
        'gateway_parameters',
        'supported_currencies',
        'extra',
    ];


    //--------
    // Casts
    //--------
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'is_crypto' => 'boolean',
            'gateway_parameters' => 'array',
            'supported_currencies' => 'array',
            'extra' => 'array',
        ];
    }


    //----------
    // Finders
    //----------
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    public static function findByAlias(string $alias): ?self
    {
        return self::where('alias', $alias)->first();
    }


    //-----------
    // Mutators
    //-----------
    protected function setCodeAttribute($value): void
    {
        $this->attributes['code'] = up($value, 20);
    }

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = up($value, 100);
    }

    protected function setAliasAttribute($value): void
    {
        $this->attributes['alias'] = up($value, 50);
    }

    protected function setImageAttribute($value): void
    {
        $this->attributes['image'] = length($value, 150);
    }


    //-----------------
    // Relationships
    //-----------------
    public function gatewayCurrencies(): HasMany
    {
        return $this->hasMany(GatewayCurrency::class);
    }
}
