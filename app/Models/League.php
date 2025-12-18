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
 * @property int $category_id
 * @property bool $is_active
 * @property string $name
 * @property string $short_name
 * @property string $slug
 * @property int|null $api_id
 * @property string|null $image
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Participant> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League byCategory(int $categoryId)
 * @method static \Database\Factories\LeagueFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|League whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class League extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;
    use JsonData;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'sports.leagues';

    protected $fillable = [
        'category_id',
        'is_active',
        'name',
        'short_name',
        'slug',
        'api_id',
        'image',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'category_id' => 'integer',
            'api_id' => 'integer',
            'is_active' => 'boolean',
            'data' => 'json',
        ];
    }


    public function formatFieldForPresentation($field, $value)
    {
        return match ($field) {
            'is_active' => $value ? 'YES' : 'NO',
            'category_id' => $value ? optional(Category::find($value))->name : $value,
            default => $value,
        };
    }


    //-----------
    // Finders
    //-----------
    public static function findByApiId(int $apiId): ?self
    {
        return self::where('api_id', $apiId)
            ->first();
    }

    public static function findByNameAndCategory(string $name, int $categoryId): ?self
    {
        return self::where('name', $name)
            ->where('category_id', $categoryId)
            ->first();
    }

    public static function findByShortNameAndCategory(string $shortName, int $categoryId): ?self
    {
        return self::where('short_name', $shortName)
            ->where('category_id', $categoryId)
            ->first();
    }

    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)
            ->first();
    }


    //--------
    // Scopes
    //--------
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }



    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = up($value, 100);
    }

    protected function setShortNameAttribute($value)
    {
        $this->attributes['short_name'] = up($value, 50);
    }

    protected function setSlugAttribute($value)
    {
        $this->attributes['slug'] = low($value, 100);
    }




    //----------------
    // Relationships
    //----------------
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }
}
