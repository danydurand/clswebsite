<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $lottery_id
 * @property int $game_id
 * @property string $name
 * @property array $draw_days
 * @property string $draw_time
 * @property int $drawn_numbers
 * @property string $stop_sale_time
 * @property string|null $api_url
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lottery $lottery
 * @method static \Database\Factories\LotteryGameFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereApiUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereDrawDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereDrawTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereDrawnNumbers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereLotteryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereStopSaleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LotteryGame whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LotteryGame extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;

    protected $table = 'lottery.lottery_games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lottery_id',
        'game_id',
        'name',
        'draw_days',
        'draw_time',
        'drawn_numbers',
        'stop_sale_time',
        'api_url',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'lottery_id' => 'integer',
        'data'       => 'json',
    ];


    //----------
    // Finders 
    //----------
    public static function findByLotteryAndGame(int $lotteryId, int $gameId): ?self
    {
        return self::where('lottery_id', $lotteryId)
            ->where('game_id', $gameId)
            ->first();
    }

    //------------
    // Attributes 
    //------------
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

    //-------------------
    // Relationships 
    //-------------------
    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }
}
