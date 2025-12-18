<?php

namespace App\Models;

use App\Domain\Concept\ConceptFrequencyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $consortium_id
 * @property string $name
 * @property bool $is_active
 * @property ConceptFrequencyEnum $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Consortium $consortium
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept byConsortium(int $consortiumId)
 * @method static \Database\Factories\ConceptFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concept whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Concept extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'name',
        'is_active',
        'frequency',
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
        'frequency' => ConceptFrequencyEnum::class,
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
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }



    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value,0,50));
    }

    //------------------
    // Relationships
    //------------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }
}
