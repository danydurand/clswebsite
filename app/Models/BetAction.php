<?php

namespace App\Models;

use App\Domain\Bet\BetActionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $bet_id
 * @property int $executed_by
 * @property \Illuminate\Support\Carbon $executed_at
 * @property BetActionEnum $action
 * @property string|null $security_code
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bet $bet
 * @property-read \App\Models\User $executedBy
 * @method static \Database\Factories\BetActionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BetAction query()
 * @mixin \Eloquent
 */
class BetAction extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'sports.bet_actions';

    protected $fillable = [
        'bet_id',
        'action',
        'executed_by',
        'executed_at',
        'security_code',
        'comments',
    ];

    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'bet_id'      => 'integer',
            'action'      => BetActionEnum::class,
            'executed_by' => 'integer',
            'executed_at' => 'datetime',
        ];
    }


    //------------
    // Mutators
    //------------
    protected function setSecurityCodeAttribute($value)
    {
        $this->attributes['security_code'] = length($value,15);
    }

    protected function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = up($value,250);
    }


    //----------------
    // Relationships
    //----------------
    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class);
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
