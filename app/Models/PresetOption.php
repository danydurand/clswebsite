<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $preset_question_id
 * @property bool $is_active
 * @property string $option
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PresetQuestion $presetQuestion
 * @property-write mixed $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption byQuestion(int $questionId)
 * @method static \Database\Factories\PresetOptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption wherePresetQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PresetOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PresetOption extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;

    protected $table = 'sports.preset_options';

    protected $fillable = [
        'preset_question_id',
        'is_active',
        'option',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'preset_question_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    //-----------
    // Finders
    //-----------
    public static function findByOptionAndPresetQuestion(string $options, int $presetQuestionId): ?self
    {
        return self::where('options', $options)
            ->where('preset_question_id', $presetQuestionId)
            ->first();
    }

    //-----------
    // Scopes
    //-----------
    public function scopeByQuestion($query, int $questionId)
    {
        return $query->where('preset_question_id', $questionId);
    }


    //-----------
    // Mutators
    //-----------
    protected function setQuestionAttribute($value)
    {
        $this->attributes['question'] = strtoupper(substr($value, 0, 100));
    }

    //----------------
    // Relationships
    //----------------
    public function presetQuestion(): BelongsTo
    {
        return $this->belongsTo(PresetQuestion::class);
    }
}
