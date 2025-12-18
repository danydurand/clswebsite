<?php

namespace App\Models;

use App\Domain\Bet\BetStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $bet_id
 * @property int $question_id
 * @property int $option_id
 * @property int $fractional_odds
 * @property string $odds
 * @property BetStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bet $bet
 * @property-read \App\Models\Option $option
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail byBet(int $betId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail byOption(int $optionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail byQuestion(int $questionId)
 * @method static \Database\Factories\BetDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereBetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereFractionalOdds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereOdds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BetDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'sports.bet_details';

    protected $fillable = [
        'bet_id',
        'question_id',
        'option_id',
        'fractional_odds',
        'odds',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'bet_id'      => 'integer',
            'question_id' => 'integer',
            'option_id'   => 'integer',
            'status'      => BetStatusEnum::class,
        ];
    }


    //----------
    // Finders
    //----------
    public static function findByBetQuestionOption(int $betId, int $questionId, int $optionId): ?self
    {
        return self::where('bet_id', $betId)
                    ->where('question_id', $questionId)
                    ->where('option_id', $optionId)
                    ->first();
    }


    //----------
    // Scopes
    //----------
    public function scopeByBet($query, int $betId)
    {
        return $query->where('bet_id', $betId);
    }

    public function scopeByQuestion($query, int $questionId)
    {
        return $query->where('question_id', $questionId);
    }

    public function scopeByOption($query, int $optionId)
    {
        return $query->where('option_id', $optionId);
    }


    //--------------
    // Attributes
    //--------------
    protected function fractionalOdds(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //-----------------
    // Relationships
    //-----------------
    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
