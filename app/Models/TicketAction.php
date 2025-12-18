<?php

namespace App\Models;

use App\Domain\Ticket\TicketActionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int $executed_by
 * @property \Illuminate\Support\Carbon $executed_at
 * @property TicketActionEnum $action
 * @property string|null $security_code
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $executedBy
 * @property-read \App\Models\Ticket $ticket
 * @method static \Database\Factories\TicketActionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereExecutedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereExecutedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereSecurityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TicketAction extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'lottery.ticket_actions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'action',
        'executed_by',
        'executed_at',
        'security_code',
        'comments',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'action'      => TicketActionEnum::class,
        'ticket_id'   => 'integer',
        'executed_by' => 'integer',
        'executed_at' => 'datetime',
    ];


    //------------
    // Mutators
    //------------
    protected function setSecurityCodeAttribute($value)
    {
        $this->attributes['security_code'] = substr($value,0,15);
    }

    protected function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = strtoupper(substr($value,0,250));
    }

    // ----------------
    // Relationships
    // ----------------
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
