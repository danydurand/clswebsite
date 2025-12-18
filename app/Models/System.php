<?php

namespace App\Models;

use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property bool $is_active
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @method static \Database\Factories\SystemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|System newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|System newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|System query()
 * @mixin \Eloquent
 */
class System extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;


    protected $fillable = [
        'is_active',
        'name',
        'prefix',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
        ];
    }


    //-----------
    // Finders
    //-----------
    public static function findByName(string $name): ?System
    {
        return self::where('name', $name)->first();
    }


    //------------
    // Mutators
    //------------
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = length($value, 50);
    }

    public function setPrefixAttribute(string $value): void
    {
        $this->attributes['prefix'] = length($value, 150);
    }


    //----------------
    // Relationships
    //----------------
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

}
