<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $payment_id
 * @property string $raffle_time
 * @property int $game_id
 * @property int $winning_factor
 * @property int|null $winner_position
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @property-read \App\Models\Payment $payment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail byGame(int $gameId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail byPayment(int $paymentId)
 * @method static \Database\Factories\PaymentDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereWinnerPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentDetail whereWinningFactor($value)
 * @mixin \Eloquent
 */
class PaymentDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'raffle_time',
        'game_id',
        'winning_factor',
        'winner_position',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'payment_id' => 'integer',
        'game_id' => 'integer',
        'winner_position' => 'integer',
    ];


    //---------
    // Scopes
    //---------
    public function scopeByPayment($query, int $paymentId)
    {
        return $query->where('payment_id', $paymentId);
    }

    public function scopeByGame($query, int $gameId)
    {
        return $query->where('game_id', $gameId);
    }


    //------------
    // Mutators
    //------------
    protected function setRaffleTimeAttribute($value)
    {
        $this->attributes['raffle_time'] = strtoupper(substr($value,0,25));
    }

    // protected function setWinnerPositionAttribute($value)
    // {
    //     $this->attributes['winner_position'] = substr($value,0,20);
    // }

    //-------------
    // Attributes
    //-------------
    protected function winningFactor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    //----------------
    // Relationships
    //----------------
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
