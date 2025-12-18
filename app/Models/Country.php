<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $currency_id
 * @property string $code
 * @property bool $is_active
 * @property string $name
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Currency $currency
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country active()
 * @method static \Database\Factories\CountryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Country extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;
    use JsonData;
    use HandleActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'currency_id',
        'is_active',
        'name',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'currency_id' => 'integer',
        'is_active' => 'boolean',
        'data' => 'json',
    ];

    //----------
    // Finders 
    //----------
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }

    //----------
    // Scopes 
    //----------


    //------------
    // Mutators
    //------------
    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(substr($value, 0, 2));
    }
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 50));
    }

    //----------------
    // Relationships
    //----------------
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
