<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $pick
 * @property bool $is_active
 * @property string $name
 * @property string $short_name
 * @property string|null $explanation
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameWinnerSequence> $gameWinnerSequences
 * @property-read int|null $game_winner_sequences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketDetail> $ticketDetails
 * @property-read int|null $ticket_details_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game active()
 * @method static \Database\Factories\GameFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game wherePick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Game extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;
    use JsonData;
    use HandleActive;

    protected $table = 'lottery.games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pick',
        'is_active',
        'name',
        'short_name',
        'explanation',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'data' => 'json',
    ];


    //----------
    // Finders
    //----------
    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    public static function findByShortName(string $shortName): ?self
    {
        return self::where('short_name', $shortName)->first();
    }



    //---------
    // Scopes
    //---------


    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 50));
    }

    protected function setShortNameAttribute($value)
    {
        $this->attributes['short_name'] = ucfirst(substr($value, 0, 3));
    }

    protected function setExplanationAttribute($value)
    {
        $this->attributes['explanation'] = substr($value, 0, 200);
    }


    //----------------
    // Relationships
    //----------------

    public function gameWinnerSequences(): HasMany
    {
        return $this->hasMany(GameWinnerSequence::class)
            ->orderBy('position_order');
    }

    public function ticketDetails(): HasMany
    {
        return $this->hasMany(TicketDetail::class);
    }

}
