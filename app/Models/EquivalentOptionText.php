<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $equivalent_question_text_id
 * @property string $api_option_name
 * @property string $real_option_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EquivalentQuestionText $equivalentQuestionText
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText byEquivalentQuestion(int $equivalentQuestionTextId)
 * @method static \Database\Factories\EquivalentOptionTextFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText whereApiOptionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText whereEquivalentQuestionTextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText whereRealOptionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquivalentOptionText whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EquivalentOptionText extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'sports.equivalent_option_texts';

    protected $fillable = [
        'equivalent_question_text_id',
        'api_option_name',
        'real_option_name',
    ];

    protected function casts(): array
    {
        return [
            'id'                          => 'integer',
            'equivalent_question_text_id' => 'integer',
        ];
    }

    //----------
    // Finders
    //----------
    public static function findByQuestionAndName(int $equivalentQuestionTextId, string $apiOptionname): ?self
    {
        return self::where('equivalent_question_text_id', $equivalentQuestionTextId)
                    ->where('api_option_name', $apiOptionname)
                    ->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeByEquivalentQuestion($query, int $equivalentQuestionTextId)
    {
        return $query->where('equivalent_question_text_id', $equivalentQuestionTextId);
    }

    //-----------
    // Mutators
    //-----------
    protected function setApiOptionNameAttribute($value)
    {
        $this->attributes['api_option_name'] = length($value,100);
    }

    protected function setRealOptionNameAttribute($value)
    {
        $this->attributes['real_option_name'] = up($value,100);
    }


    //----------------
    // Relationships
    //----------------
    public function equivalentQuestionText(): BelongsTo
    {
        return $this->belongsTo(EquivalentQuestionText::class);
    }
}
