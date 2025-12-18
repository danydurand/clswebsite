<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property bool $is_active
 * @property string $name
 * @property string $slug
 * @property string|null $icon
 * @property array<array-key, mixed>|null $tournament_countries_codes
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\League> $leagues
 * @property-read int|null $leagues_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Participant> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereTournamentCountriesCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use HandleActive;
    use JsonData;
    use HasRelatedRecords;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'sports.categories';

    protected $fillable = [
        'is_active',
        'name',
        'slug',
        'icon',
        'tournament_countries_codes',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'consortium_id' => 'integer',
            'is_active' => 'boolean',
            'data' => 'array',
            'tournament_countries_codes' => 'array',
        ];
    }

    public function formatFieldForPresentation($field, $value)
    {
        return match ($field) {
            'is_active' => $value ? 'YES' : 'NO',
            'user_id' => $value ? optional(User::find($value))->username : $value,
            default => $value,
        };
    }

    //-----------
    // Finders
    //-----------
    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)
            ->first();
    }

    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)
            ->first();
    }


    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = up($value, 100);
    }

    protected function setSlugAttribute($value)
    {
        $this->attributes['slug'] = low($value, 100);
    }

    protected function setIconAttribute($value)
    {
        $this->attributes['icon'] = strlen($value) > 0
            ? length($value, 50)
            : null;
    }


    //---------------
    // Relationships
    //---------------
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }

    public function participants(): HasManyThrough
    {
        return $this->hasManyThrough(Participant::class, League::class);
    }

}
