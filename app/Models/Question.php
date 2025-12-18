<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Traits\JsonData;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $event_id
 * @property int $category_id
 * @property string $title
 * @property bool $is_active
 * @property int $is_checked
 * @property bool $refund
 * @property bool $was_translated
 * @property bool|null $is_locked
 * @property int|null $win_option_id
 * @property int|null $amount_refunded
 * @property int|null $api_id
 * @property int|null $preset_question_id
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BetDetail> $betDetails
 * @property-read int|null $bet_details_count
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Option> $options
 * @property-read int|null $options_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question byCategory(int $eventId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question byEvent(int $eventId)
 * @method static \Database\Factories\QuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question untranslated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereAmountRefunded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereIsLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question wherePresetQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereRefund($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereWasTranslated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereWinOptionId($value)
 * @property-read mixed $win_option_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question tomorrow()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question yesterday()
 * @mixin \Eloquent
 */
class Question extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;
    use JsonData;

    protected $table = 'sports.questions';


    protected $fillable = [
        'event_id',
        'category_id',
        'title',
        'is_active',
        'is_locked',
        'was_translated',
        'is_checked',
        'refund',
        'win_option_id',
        'amount_refunded',
        'api_id',
        'preset_question_id',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'event_id' => 'integer',
            'category_id' => 'integer',
            'api_id' => 'integer',
            'preset_question_id' => 'integer',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
            'is_checked' => 'boolean',
            'refund' => 'boolean',
        ];
    }

    public $appends = [
        'win_option_name',
    ];

    //-----------
    // Finders
    //-----------
    public static function findByApiId(int $apiId): ?self
    {
        return self::where('api_id', $apiId)
            ->first();
    }

    public static function findByTitleAndEvent(string $title, int $eventId): ?self
    {
        return self::where('title', $title)
            ->where('event_id', $eventId)
            ->first();
    }


    //-----------
    // Scopes
    //-----------
    public function scopeLast7Days($query)
    {
        $last7Days = now()->subDay(7)->toDateString();
        $query->whereHas('event', function ($q) use ($last7Days) {
            $q->whereDate('start_time', '>=', $last7Days);
        });
        return $query;
    }

    public function scopeYesterday($query)
    {
        $yesterday = now()->subDay()->toDateString();
        $query->whereHas('event', function ($q) use ($yesterday) {
            $q->whereDate('start_time', $yesterday);
        });
        return $query;
    }

    public function scopeToday($query)
    {
        $today = now()->toDateString();
        $query->whereHas('event', function ($q) use ($today) {
            $q->whereDate('start_time', $today);
        });
        return $query;
    }

    public function scopeTomorrow($query)
    {
        $tomorrow = now()->addDay()->toDateString();
        $query->whereHas('event', function ($q) use ($tomorrow) {
            $q->whereDate('start_time', $tomorrow);
        });
        return $query;
    }

    public function scopeByEvent($query, int $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByCategory($query, int $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeUntranslated($query)
    {
        return $query->where('was_translated', false);
    }

    public function scopeUnchecked($query)
    {
        return $query->where('is_checked', false);
    }


    //-----------
    // Mutators
    //-----------
    protected function setTitleAttribute($value)
    {
        $this->attributes['title'] = up($value, 100);
    }

    //-------------
    // Attributes
    //-------------
    protected function winOptionName(): Attribute
    {
        $winOptionName = $this->win_option_id
            ? Option::find($this->win_option_id)->name
            : null;

        return Attribute::make(
            get: fn($value) => $winOptionName,
        );
    }

    protected function amountRefunded(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function betDetails(): HasMany
    {
        return $this->hasMany(BetDetail::class);
    }

}
