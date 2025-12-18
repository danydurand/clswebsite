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
 * @property int $question_id
 * @property string $name
 * @property bool $is_active
 * @property bool $is_locked
 * @property bool $is_winner
 * @property bool $was_translated
 * @property string|null $odds
 * @property int|null $api_id
 * @property int|null $preset_option_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BetDetail> $betDetails
 * @property-read int|null $bet_details_count
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option byQuestion(int $questionId)
 * @method static \Database\Factories\OptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option untranslated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereIsLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereIsWinner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereOdds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option wherePresetOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Option whereWasTranslated($value)
 * @mixin \Eloquent
 */
class Option extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;

    protected $table = 'sports.options';

    protected $fillable = [
        'question_id',
        'name',
        'odds',
        'is_active',
        'was_translated',
        'is_winner',
        'api_id',
        'preset_option_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'question_id' => 'integer',
            'api_id' => 'integer',
            'preset_option_id' => 'integer',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
            'is_winner' => 'boolean',
            'was_translated' => 'boolean',
        ];
    }

    //----------
    // Finders
    //----------
    public static function findByApiId(int $apiId): ?self
    {
        return self::where('api_id', $apiId)
            ->first();
    }

    public static function findByNameAndQuestion(string $name, int $questionId): ?self
    {
        return self::where('name', $name)
            ->where('question_id', $questionId)
            ->first();
    }


    //----------
    // Scopes
    //----------
    public function scopeByQuestion($query, int $questionId)
    {
        return $query->where('question_id', $questionId);
    }

    public function scopeUntranslated($query)
    {
        return $query->where('was_translated', false);
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = up($value, 100);
    }


    //----------------
    // Relationships
    //----------------
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function betDetails(): HasMany
    {
        return $this->hasMany(BetDetail::class);
    }
}
