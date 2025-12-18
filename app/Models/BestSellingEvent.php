<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $id
 * @property int|null $consortium_id
 * @property int|null $supervisor_id
 * @property int|null $group_id
 * @property int|null $bank_id
 * @property string|null $date
 * @property int|null $event_id
 * @property string|null $event_slug
 * @property int|null $qty
 * @property int|null $stake_amount
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereEventSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereStakeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingEvent whereSupervisorId($value)
 * @mixin \Eloquent
 */
class BestSellingEvent extends Model
{
    protected $table = 'sports.best_selling_events_view';


    //----------------
    // Relationships
    //----------------
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }


}
