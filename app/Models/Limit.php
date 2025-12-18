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
 * @property bool $is_active
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Banker|null $banker
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \App\Models\Consortium $consortium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LimitDetail> $details
 * @property-read int|null $details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seller> $sellers
 * @property-read int|null $sellers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Terminal> $terminals
 * @property-read int|null $terminals_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit byConsortium(int $consortiumId)
 * @method static \Database\Factories\LimitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit whereBankerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Limit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Limit extends Model implements \OwenIt\Auditing\Contracts\Auditable
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
        'is_active',
        'name',
        'banker_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'consortium_id' => 'integer',
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
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }

    //-----------------
    // Relationships
    //-----------------
    public function details(): HasMany
    {
        return $this->hasMany(LimitDetail::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class);
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function banker(): BelongsTo
    {
        return $this->belongsTo(Banker::class);
    }

}
