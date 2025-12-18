<?php

namespace App\Models;

use App\Models\Bet;
use App\Models\Question;
use App\Models\BetDetail;
use App\Models\Traits\JsonData;
use App\Domain\Event\EventStatusEnum;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $api_id
 * @property EventStatusEnum $status
 * @property int $category_id
 * @property int $home_participant_id
 * @property int $away_participant_id
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $bet_start_time
 * @property \Illuminate\Support\Carbon $bet_end_time
 * @property string|null $changes_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $slug
 * @property array<array-key, mixed>|null $home_score
 * @property array<array-key, mixed>|null $away_score
 * @property array<array-key, mixed>|null $changes
 * @property array<array-key, mixed>|null $data
 * @property-read \App\Models\Participant $awayParticipant
 * @property-read \App\Models\Category $category
 * @property-read mixed $checked
 * @property-read mixed $final_away_score
 * @property-read mixed $final_home_score
 * @property-read \App\Models\Participant $homeParticipant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Question> $questions
 * @property-read int|null $questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bet[] $bets
 * @property-read int|null $bets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event byStatus(int $status)
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAwayParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAwayScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereBetEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereBetStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereChangesTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereHomeParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereHomeScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @property int $status_code_id
 * @property-read mixed $process_checking_id
 * @property-read mixed $short_slug
 * @property-read \App\Models\StatusCode $statusCode
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event byStatusCode(int $statusCode)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event finished()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event tomorrow()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event yesterday()
 * @property bool $is_locked
 * @property int|null $check_id
 * @property int|null $qty_bets
 * @property int|null $qty_winners
 * @property int|null $qty_losers
 * @property int|null $total_stake_amount
 * @property int|null $total_return_amount
 * @property int|null $profit
 * @property-read \App\Models\Check|null $check
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event betEnd(string $day)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event byCheck(int $checkId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event locked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event unfinished()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event unlocked()
 * @mixin \Eloquent
 */
class Event extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;
    use HasRelatedRecords;
    use HasRelationships;

    protected $table = 'sports.events';

    protected $fillable = [
        'category_id',
        'home_participant_id',
        'away_participant_id',
        'status_code_id',
        'api_id',
        'slug',
        'home_score',
        'away_score',
        'changes',
        'changes_time',
        'start_time',
        'bet_start_time',
        'bet_end_time',
        'check_id',
        'qty_bets',
        'qty_winners',
        'qty_losers',
        'total_stake_amount',
        'total_return_amount',
        'profit',
        'is_locked',
        'data',
    ];


    protected function casts(): array
    {
        return [
            'id'                  => 'integer',
            'status_code_id'      => 'integer',
            'category_id'         => 'integer',
            'home_participant_id' => 'integer',
            'away_participant_id' => 'integer',
            'check_id'            => 'integer',
            'home_score'          => 'array',
            'away_score'          => 'array',
            'start_time'          => 'datetime',
            'bet_start_time'      => 'datetime',
            'bet_end_time'        => 'datetime',
            'api_id'              => 'integer',
            'status'              => EventStatusEnum::class,
            'changes'             => 'array',
            'data'                => 'array',
            'is_locked'           => 'boolean',
        ];
    }

    public $appends = [
        'final_home_score',
        'final_away_score',
        'short_slug',
        'checked'
    ];

    //----------
    // Finders
    //----------
    public static function findByApiId(int $apiId): ?self
    {
        return self::where('api_id', $apiId)
                    ->first();
    }

    public static function findByHomeAwayStart(int $homeParticipantId, int $awayParticipantId, $startTime): ?self
    {
        return self::where('home_participant_id', $homeParticipantId)
                    ->where('away_participant_id', $awayParticipantId)
                    ->where('start_time', $startTime)
                    ->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeByCheck($query, int $checkId)
    {
        return $query->where('check_id', $checkId);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function scopeUnchecked($query)
    {
        return $query->whereNull('check_id');
    }

    public function scopeByStatusCode($query, int $statusCode)
    {
        return $query->where('statusCode', $statusCode);
    }

    public function scopeOpen($query)
    {
        $finishedStatusCodes = StatusCode::meansFinished()->pluck('id');
        return $query->whereNotIn('status', $finishedStatusCodes);
    }

    public function scopeFinished($query)
    {
        $finishedStatusCodes = StatusCode::meansFinished()->pluck('id');
        return $query->whereIn('status_code_id', $finishedStatusCodes);
    }

    public function scopeUnfinished($query)
    {
        $finishedStatusCodes = StatusCode::meansFinished()->pluck('id');
        return $query->whereNotIn('status_code_id', $finishedStatusCodes);
    }

    public function scopeToday($query)
    {
        $today = now();
        return $query->whereDate('start_time', $today->toDateString());
    }

    public function scopeYesterday($query)
    {
        $yesterday = now()->subDay();
        return $query->whereDate('start_time', $yesterday->toDateString());
    }

    public function scopeTomorrow($query)
    {
        $tomorrow = now()->addDay();
        return $query->whereDate('start_time', $tomorrow->toDateString());
    }

    public function scopeBetEnd($query, string $day)
    {
        return $query->where('bet_end_time', '<=', $day);
    }


    //-------------
    // Attributes
    //-------------
    protected function checked(): Attribute
    {
        return Attribute::make(
            get: fn ($value): bool => $this->check_id !== null
        );
    }

    protected function shortSlug(): Attribute
    {
        $slugElements = explode('-', $this->slug);
        array_pop($slugElements);
        $shortSlug = implode('-', $slugElements);
        return Attribute::make(
            get: fn ($value) => $shortSlug,
        );
    }

    protected function finalHomeScore(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->home_score['current'] ?? null,
        );
    }

    protected function finalAwayScore(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->away_score['current'] ?? null,
        );
    }

    protected function totalStakeAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function totalReturnAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function profit(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function check(): BelongsTo
    {
        return $this->belongsTo(Check::class);
    }

    public function statusCode(): BelongsTo
    {
        return $this->belongsTo(StatusCode::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function homeParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'home_participant_id');
    }

    public function awayParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'away_participant_id');
    }

    public function bets(): \Staudenmeir\EloquentHasManyDeep\HasManyDeep
    {
        return $this->hasManyDeep(
            Bet::class,
            [Question::class, BetDetail::class],
            ['event_id', 'question_id', 'id'],
            ['id', 'id', 'bet_id']
        );
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

}
