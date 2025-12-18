<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_active
 * @property bool $is_public
 * @property int $price
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Consortium> $consortia
 * @property-read int|null $consortia_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate active()
 * @method static \Database\Factories\RateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Rate extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;
    use HasRelatedRecords;
    use HandleActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_active',
        'is_public',
        'price',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    //----------
    // Finders
    //----------
    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 50));
    }

    //-----------------
    // Attributes
    //-----------------
    protected function price(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    //-----------------
    // Relationships
    //-----------------
    public function consortia(): HasMany
    {
        return $this->hasMany(Consortium::class);
    }
}
