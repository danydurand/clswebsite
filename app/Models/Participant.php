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
 * $data json
 * $is_active boolean
 *
 * @property int $id
 * @property int $league_id
 * @property bool $is_active
 * @property string $name
 * @property string $slug
 * @property string $short_name
 * @property int|null $api_id
 * @property string|null $image
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $awayEvents
 * @property-read int|null $away_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $homeEvents
 * @property-read int|null $home_events_count
 * @property-read \App\Models\League $league
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant byLeague(int $leagueId)
 * @method static \Database\Factories\ParticipantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereLeagueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Participant extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;
    use JsonData;
    use HasRelatedRecords;

    protected $table = 'sports.participants';

    protected $fillable = [
        'league_id',
        'is_active',
        'name',
        'slug',
        'short_name',
        'api_id',
        'image',
        'data',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'league_id' => 'integer',
            'api_id' => 'integer',
            'is_active' => 'boolean',
            'data' => 'json',
        ];
    }


    //-----------
    // Finders
    //-----------
    public static function findByApiId(int $apiId): ?self
    {
        return self::where('api_id', $apiId)
            ->first();
    }

    public static function findByNameAndLeague(string $name, int $leagueId): ?self
    {
        return self::where('name', $name)
            ->where('league_id', $leagueId)
            ->first();
    }

    public static function findByShortNameAndLeague(string $shortName, int $leagueId): ?self
    {
        return self::where('short_name', $shortName)
            ->where('league_id', $leagueId)
            ->first();
    }

    public static function findBySlugAndLeague(string $slug, int $leagueId): ?self
    {
        return self::where('slug', $slug)
            ->where('league_id', $leagueId)
            ->first();
    }


    //---------
    // Scopes
    //---------
    public function scopeByLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
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
        $this->attributes['short_name'] = low($value, 50);
    }

    protected function setSlugAttribute($value)
    {
        $this->attributes['slug'] = low($value, 100);
    }



    //----------------
    // Relationships
    //----------------
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function homeEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'home_participant_id');
    }

    public function awayEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'away_participant_id');
    }
}
