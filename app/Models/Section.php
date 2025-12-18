<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $system_id
 * @property bool $is_active
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \App\Models\System $system
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section bySystem(int $systemId)
 * @method static \Database\Factories\SectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @mixin \Eloquent
 */
class Section extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;

    protected $fillable = [
        'system_id',
        'is_active',
        'position',
        'name',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'system_id' => 'integer',
            'position' => 'integer',
            'is_active' => 'boolean',
        ];
    }


    //-----------
    // Finders
    //-----------
    public static function findBySystemAndName(int $systemId, string $name): ?Section
    {
        return self::where('system_id', $systemId)
            ->where('name', $name)
            ->first();
    }

    //-----------
    // Scopes
    //-----------
    public function scopeBySystem($query, int $systemId)
    {
        return $query->where('sections.system_id', $systemId);
    }


    //------------
    // Mutators
    //------------
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = length($value, 50);
    }




    //----------------
    // Relationships
    //----------------
    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
