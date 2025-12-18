<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $lottery_id
 * @property string $raffle_time
 * @property array $lottery_games_ids
 * @property array $draw_days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lottery $lottery
 * @method static \Database\Factories\RaffleTimesDependenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence whereDrawDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence whereLotteryGamesIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence whereLotteryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RaffleTimesDependence whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RaffleTimesDependence extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'lottery.raffle_times_dependences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lottery_id',
        'raffle_time',
        'lottery_games_ids',
        'draw_days',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'lottery_id' => 'integer',
    ];


    //----------
    // Finders 
    //----------
    public static function findByLotteryAndTime(int $lotteryId, string $raffleTime)
    {
        return self::where('lottery_id', $lotteryId)
                    ->where('raffle_time', $raffleTime)
                    ->first();
    }
    
    

    //------------
    // Attributes 
    //------------
    /**
     * Get the lottery_game_ids attribute as an array.
     *
     * @param  string  $value
     * @return array
     */
    public function getLotteryGamesIdsAttribute($value)
    {
        return explode(', ', $value);
    }

    /**
     * Set the lottery_game_ids attribute as a comma-separated string.
     *
     * @param  array  $value
     * @return void
     */
    public function setLotteryGamesIdsAttribute($value)
    {
        $this->attributes['lottery_games_ids'] = implode(', ', $value);
    }

    /**
     * Get the draw_days attribute as an array.
     *
     * @param  string  $value
     * @return array
     */
    public function getDrawDaysAttribute($value)
    {
        return explode(', ', $value);
    }

    /**
     * Set the draw_days attribute as a comma-separated string.
     *
     * @param  array  $value
     * @return void
     */
    public function setDrawDaysAttribute($value)
    {
        $this->attributes['draw_days'] = implode(', ', $value);
    }

    //----------------
    // Relationships
    //----------------
    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }
}
