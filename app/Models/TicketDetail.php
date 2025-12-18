<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int $raffle_id
 * @property int $game_id
 * @property int $stake_amount
 * @property int $prize_amount
 * @property int $commission_perc
 * @property int $commission
 * @property int $profit
 * @property bool $is_valid_bet
 * @property string $sequence
 * @property int|null $winning_factor
 * @property bool|null $won
 * @property int|null $scrutiny_id
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $commision
 * @property mixed $commision_perc
 * @property-read \App\Models\Game $game
 * @property-read \App\Models\Raffle $raffle
 * @property-read \App\Models\Scrutiny|null $scrutiny
 * @property-read \App\Models\Ticket $ticket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail byRaffle(int $raffleId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail byTicket(int $ticketId)
 * @method static \Database\Factories\TicketDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail isValid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereCommissionPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereIsValidBet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail wherePrizeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereRaffleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereScrutinyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereStakeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereWinningFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail whereWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketDetail won()
 * @mixin \Eloquent
 */
class TicketDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;

    protected $table = 'lottery.ticket_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'raffle_id',
        'game_id',
        'sequence',
        'stake_amount',
        'won',
        'prize_amount',
        'commission_perc',
        'commission',
        'profit',
        'is_valid_bet',
        'winning_factor',
        'scrutiny_id',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'ticket_id'    => 'integer',
        'raffle_id'    => 'integer',
        'game_id'      => 'integer',
        'scrutiny_id'  => 'integer',
        'won'          => 'boolean',
        'is_valid_bet' => 'boolean',
        'data'         => 'array',
    ];

    //----------
    // Finders
    //----------


    //----------
    // Scopes
    //----------
    public function scopeByTicket($query, int $ticketId)
    {
        return $query->where('ticket_id', $ticketId);
    }

    public function scopeByRaffle($query, int $raffleId)
    {
        return $query->where('raffle_id', $raffleId);
    }

    public function scopeWon($query)
    {
        return $query->where('won', true);
    }

    public function scopeIsValid($query)
    {
        return $query->where('is_valid_bet', true);
    }




    //-------------
    // Attributes
    //-------------
    protected function stakeAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function winningFactor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function prizeAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function commisionPerc(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function commision(): Attribute
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
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function scrutiny(): BelongsTo
    {
        return $this->belongsTo(Scrutiny::class);
    }
}
