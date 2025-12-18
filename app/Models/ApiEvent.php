<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $api_event_id
 * @property \Illuminate\Support\Carbon $event_date
 * @property array<array-key, mixed> $tournament
 * @property array<array-key, mixed>|null $season
 * @property array<array-key, mixed>|null $home_team
 * @property array<array-key, mixed>|null $away_team
 * @property array<array-key, mixed>|null $status
 * @property array<array-key, mixed>|null $home_score
 * @property array<array-key, mixed>|null $away_score
 * @property array<array-key, mixed>|null $changes
 * @property int|null $changes_time
 * @property string|null $start_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $event_id
 * @property \Illuminate\Support\Carbon|null $converted_at
 * @property bool|null $has_conversion_error
 * @property bool|null $must_be_updated
 * @property \Illuminate\Support\Carbon|null $was_updated_at
 * @property string|null $conversion_message
 * @property string|null $updating_message
 * @property array<array-key, mixed>|null $data
 * @property-read \App\Models\Event|null $event
 * @property-read mixed $away_team_country_code
 * @property-read mixed $away_team_country_name
 * @property-read mixed $away_team_id
 * @property-read mixed $away_team_name
 * @property-read mixed $away_team_name_code
 * @property-read mixed $away_team_slug
 * @property-read mixed $category_name
 * @property-read mixed $category_slug
 * @property-read mixed $home_team_country_code
 * @property-read mixed $home_team_country_name
 * @property-read mixed $home_team_id
 * @property-read mixed $home_team_name
 * @property-read mixed $home_team_name_code
 * @property-read mixed $home_team_slug
 * @property-read mixed $league_id
 * @property-read mixed $league_name
 * @property-read mixed $league_slug
 * @property-read mixed $slug
 * @property-read mixed $status_code
 * @property-read mixed $status_description
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent byCategory(string $categorySlug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent byChangeTime(string $changeTime)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent byCountry(string $countryCode)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent byEventDate(string $eventDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent byLeague(string $leagueSlug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent byStatusCode(string $statusCode)
 * @method static \Database\Factories\ApiEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent mustBeUpdated()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent pendingToConvert()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereApiEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereAwayScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereAwayTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereChangesTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereConversionMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereConvertedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereHasConversionError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereHomeScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereHomeTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereMustBeUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereTournament($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereUpdatingMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent whereWasUpdatedAt($value)
 * @property-read mixed $status_type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent today()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent tomorrow()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent yesterday()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiEvent pendingToUpdate()
 * @mixin \Eloquent
 */
class ApiEvent extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;

    protected $table = 'sports.api_events';

    protected $fillable = [
        'api_event_id',
        'event_date',
        'tournament',
        'season',
        'home_team',
        'away_team',
        'status',
        'home_score',
        'away_score',
        'changes',
        'changes_time',
        'start_time',
        'event_id',
        'converted_at',
        'has_conversion_error',
        'conversion_message',
        'must_be_updated',
        'was_updated_at',
        'updating_message',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'id'                   => 'integer',
            'api_event_id'         => 'integer',
            'event_date'           => 'date',
            'tournament'           => 'array',
            'season'               => 'array',
            'home_team'            => 'array',
            'away_team'            => 'array',
            'status'               => 'array',
            'home_score'           => 'array',
            'away_score'           => 'array',
            'converted_at'         => 'datetime',
            'must_be_updated'      => 'boolean',
            'was_updated_at'       => 'date',
            'has_conversion_error' => 'boolean',
            'changes'              => 'array',
            'changes_time'         => 'timestamp',
            'data'                 => 'array',
        ];
    }


    //---------
    // Scopes
    //---------
    public function scopeToday($query)
    {
        $today = now();
        return $query->where('event_date', $today->toDateString());
    }

    public function scopeYesterday($query)
    {
        $yesterday = now()->subDay();
        return $query->where('event_date', $yesterday->toDateString());
    }

    public function scopeTomorrow($query)
    {
        $tomorrow = now()->addDay();
        return $query->where('event_date', $tomorrow->toDateString());
    }

    public function scopePendingToUpdate($query)
    {
        // records with changes on the api_events table not reflected on the real events table
        return $query->where('updating_message', null);
    }

    public function scopeMustBeUpdated($query)
    {
        // records with changes on the api_events table not reflected on the real events table
        return $query->where('must_be_updated', true);
    }

    public function scopePendingToConvert($query)
    {
        // Pending to convert to real event in the platform
        return $query->whereNull('event_id');
    }

    public function scopeByEventDate($query, string $eventDate)
    {
        return $query->where('event_date', $eventDate);
    }

    public function scopeByChangeTime($query, string $changeTime)
    {
        return $query->whereDate('changes_time', $changeTime);
    }

    public function scopeByCategory($query, string $categorySlug)
    {
        return $query->where('tournament->category->sport->slug', $categorySlug);
    }

    public function scopeByLeague($query, string $leagueSlug)
    {
        return $query->where('tournament->slug', $leagueSlug);
    }

    public function scopeByCountry($query, string $countryCode)
    {
        return $query->where('tournament->category->country->alpha2', $countryCode);
    }

    public function scopeByStatusCode($query, string $statusCode)
    {
        // Status Codes: (0 : Not Started, 60: Postponed, 70: Canceled, 100: Finished)
        return $query->where('tournament->status->code', $statusCode);
    }


    //-------------
    // Mutators
    //-------------
    // protected function setConversionMessageAttribute($value)
    // {
    //     $this->attributes['conversion_message'] = up($value,100);
    // }

    // protected function setUpdatingMessageAttribute($value)
    // {
    //     $this->attributes['updating_message'] = up($value,100);
    // }


    //-------------
    // Attributes
    //-------------
    public function getSlugAttribute($value)
    {
        return $this->data['slug'];
    }

    public function getCategoryNameAttribute($value)
    {
        return $this->tournament['category']['sport']['name'];
    }

    public function getCategorySlugAttribute($value)
    {
        return $this->tournament['category']['sport']['slug'];
    }

    public function getLeagueIdAttribute($value)
    {
        return $this->tournament['id'];
    }

    public function getLeagueNameAttribute($value)
    {
        return $this->tournament['name'];
    }

    public function getLeagueSlugAttribute($value)
    {
        return $this->tournament['slug'];
    }

    public function getHomeTeamIdAttribute($value)
    {
        return $this->home_team['id'];
    }

    public function getHomeTeamNameAttribute($value)
    {
        return $this->home_team['name'];
    }

    public function getHomeTeamSlugAttribute($value)
    {
        return $this->home_team['slug'];
    }

    public function getHomeTeamNameCodeAttribute($value)
    {
        return $this->home_team['nameCode'];
    }

    public function getHomeTeamCountryNameAttribute($value)
    {
        return $this->home_team['country']['name'];
    }

    public function getHomeTeamCountryCodeAttribute($value)
    {
        return $this->home_team['country']['alpha2'];
    }

    public function getAwayTeamIdAttribute($value)
    {
        return $this->away_team['id'];
    }

    public function getAwayTeamNameAttribute($value)
    {
        return $this->away_team['name'];
    }

    public function getAwayTeamSlugAttribute($value)
    {
        return $this->away_team['slug'];
    }

    public function getAwayTeamNameCodeAttribute($value)
    {
        return $this->away_team['nameCode'];
    }

    public function getAwayTeamCountryNameAttribute($value)
    {
        return $this->away_team['country']['name'];
    }

    public function getAwayTeamCountryCodeAttribute($value)
    {
        return $this->away_team['country']['alpha2'];
    }

    public function getStatusCodeAttribute($value)
    {
        return $this->status['code'];
    }

    public function getStatusTypeAttribute($value)
    {
        return $this->status['type'];
    }

    public function getStatusDescriptionAttribute($value)
    {
        return $this->status['description'];
    }


    //----------------
    // Relationships
    //----------------
    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

}
