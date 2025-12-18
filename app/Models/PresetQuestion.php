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
 * @property bool $is_active
 * @property string $title
 * @property int|null $category_id
 * @property array<array-key, mixed>|null $excluded_categories_ids
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PresetOption> $presetOptions
 * @property-read int|null $preset_options_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion allCategories()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion byCategory($categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion except(int $categoryId)
 * @method static \Database\Factories\PresetQuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion whereExcludedCategoriesIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetQuestion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PresetQuestion extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;

    protected $table = 'sports.preset_questions';

    protected $fillable = [
        'is_active',
        'title',
        'category_id',
        'excluded_categories_ids',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'category_id' => 'integer',
            'excluded_categories_ids' => 'array',
        ];
    }

    //-----------
    // Finders
    //-----------
    public static function findByTitleAndCategory(string $title, ?int $categoryId): ?self
    {
        return self::where('title', $title)
            ->where('category_id', $categoryId)
            ->first();
    }

    //----------
    // Scopes
    //----------
    public function scopeExcept($query, int $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->whereNull('excluded_categories_ids')
                ->orWhereRaw('NOT (excluded_categories_ids::jsonb @> ?)', [json_encode([$categoryId])]);
        });
    }

    public function scopeAllCategories($query)
    {
        return $query->where('category_id', null);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    //-----------
    // Mutators
    //-----------
    protected function setTitleAttribute($value)
    {
        $this->attributes['title'] = strtoupper(substr($value, 0, 100));
    }


    //----------------
    // Relationships
    //----------------
    public function presetOptions(): HasMany
    {
        return $this->hasMany(PresetOption::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
