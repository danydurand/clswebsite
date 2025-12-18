<?php

namespace App\Models;

use App\Services\AuthUser;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $consortium_id
 * @property int|null $banker_id
 * @property string $name
 * @property bool $is_active
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Banker|null $banker
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \App\Models\Consortium $consortium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RestrictionDetail> $restrictionDetails
 * @property-read int|null $restriction_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seller> $sellers
 * @property-read int|null $sellers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction byConsortium(int $consortiumId)
 * @method static \Database\Factories\RestrictionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereBankerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restriction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Restriction extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'name',
        'is_active',
        'banker_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'consortium_id' => 'integer',
        'is_active' => 'boolean',
        'banker_id' => 'integer',
    ];

    //----------
    // Finders
    //----------
    public static function findByConsortiumAndName(int $consortiumId, string $name): ?self
    {
        return self::where('consortium_id', $consortiumId)
            ->where('name', $name)
            ->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeByConsortium($query, ?int $consortiumId)
    {
        return $consortiumId !== null ? $query->where('consortium_id', $consortiumId) : $query;
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 50));
    }

    //----------------
    // Relationships
    //----------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function banker(): BelongsTo
    {
        return $this->belongsTo(Banker::class);
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }


    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function restrictionDetails(): HasMany
    {
        return $this->hasMany(RestrictionDetail::class);
    }
}
