<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $lottery_game_id
 * @property int $draw_number
 * @property \Illuminate\Support\Carbon $draw_date
 * @property string $draw_time
 * @property string $result
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LotteryGame $lotteryGame
 * @method static \Database\Factories\DrawingResultFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereDrawDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereDrawNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereDrawTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereLotteryGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawingResult whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DrawingResult extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'lottery.drawing_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lottery_game_id',
        'draw_number',
        'draw_date',
        'draw_time',
        'result',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'lottery_game_id' => 'integer',
        'draw_date'       => 'date',
    ];


    //----------
    // Finders 
    //----------
    public static function findByLotteryGameAndDrawNumber(int $lotteryGameId, int $drawNumber): ?self
    {
        return self::where('lottery_game_id', $lotteryGameId)
                    ->where('draw_number', $drawNumber)
                    ->first();
    }


    //-------------------
    // Relationships
    //-------------------
    public function lotteryGame(): BelongsTo
    {
        return $this->belongsTo(LotteryGame::class);
    }
}
