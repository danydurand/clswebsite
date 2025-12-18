<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Country> $countries
 * @property-read int|null $countries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency except($countryId)
 * @method static \Database\Factories\CurrencyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Currency whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Currency extends Model implements \OwenIt\Auditing\Contracts\Auditable
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
        'code',
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    //----------
    // Appends
    //----------
    public $appends = [
    ];

    //----------
    // Finders 
    //----------
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }



    //------------
    // Scopes
    //------------
    public function scopeActives($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeExcept($query, $countryId)
    {
        return $query->where('id', '!=', $countryId);
    }



    //------------
    // Mutators
    //------------
    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(substr($value, 0, 3));
    }
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 50));
    }

    //-------------
    // Attributes
    //-------------


    //----------------
    // Relationships
    //----------------
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }

}
