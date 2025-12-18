<?php

namespace App\Models;

use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $category_id
 * @property string $api_question_title
 * @property string $real_question_title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EquivalentOptionText> $equivalentOptionTexts
 * @property-read int|null $equivalent_option_texts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText byCategory(int $categoryId)
 * @method static \Database\Factories\EquivalentQuestionTextFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText whereApiQuestionTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText whereRealQuestionTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentQuestionText whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EquivalentQuestionText extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;

    protected $table = 'sports.equivalent_question_texts';

    protected $fillable = [
        'category_id',
        'api_question_title',
        'real_question_title',
    ];


    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'category_id' => 'integer',
        ];
    }

    //----------
    // Finders
    //----------
    public static function findByCategoryAndTitle(int $categoryId, string $title): ?self
    {
        return self::where('category_id', $categoryId)
                    ->where('real_question_title', $title)
                    ->first();
    }

    public static function findByCategoryAndApiTitle(int $categoryId, string $title): ?self
    {
        return self::where('category_id', $categoryId)
                    ->where('api_question_title', $title)
                    ->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }



    //-----------
    // Mutators
    //-----------
    protected function setApiQuestionTitleAttribute($value)
    {
        $this->attributes['api_question_title'] = length($value,100);
    }

    protected function setRealQuestionTitleAttribute($value)
    {
        $this->attributes['real_question_title'] = up($value,100);
    }


    //---------------
    // Relationships
    //---------------
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function equivalentOptionTexts(): HasMany
    {
        return $this->hasMany(EquivalentOptionText::class);
    }


}
