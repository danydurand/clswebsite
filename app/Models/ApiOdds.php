<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $api_event_id
 * @property \Illuminate\Support\Carbon $event_date
 * @property array<array-key, mixed> $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $transferred_at
 * @property bool|null $has_transfering_error
 * @property string|null $transfering_message
 * @property-read mixed $choices
 * @method static \Database\Factories\ApiOddsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereApiEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereHasTransferingError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereTransferingMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereTransferredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiOdds whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ApiOdds extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'sports.api_odds';

    protected $fillable = [
        'api_event_id',
        'event_date',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'id'           => 'integer',
            'api_event_id' => 'integer',
            'event_date'   => 'date',
            'data'         => 'array',
        ];
    }

    //----------
    // Finders
    //----------
    public static function findByApiEventId(int $apiEventId): ?self
    {
        return self::where('api_event_id', $apiEventId)->first();
    }

    //-------------
    // Attributes
    //-------------
    public function getChoicesAttribute($value)
    {
        return $this->data['choices'];
    }


    //-----------------
    // Relationships
    //-----------------
}
