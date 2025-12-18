<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use App\Models\Traits\HasRelatedRecords;
use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $is_active
 * @property bool $auto_create_raffles
 * @property array<array-key, mixed> $raffle_times
 * @property string|null $image
 * @property string|null $colour
 * @property string|null $api_url
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $qty_raffles_to_create
 * @property-read array $times
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LotteryGame> $lotteryGames
 * @property-read int|null $lottery_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RaffleTimesDependence> $raffleTimesDependences
 * @property-read int|null $raffle_times_dependences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Raffle> $raffles
 * @property-read int|null $raffles_count
 * @property-write mixed $color
 * @property-write mixed $raffle_colors
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery autoCR()
 * @method static \Database\Factories\LotteryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereApiUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereAutoCreateRaffles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereColour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereRaffleTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lottery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lottery extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;
    use HasRelatedRecords;
    use HandleActive;

    protected $table = 'lottery.lotteries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'is_active',
        'raffle_times',
        'auto_create_raffles',
        'colour',
        'image',
        'api_url',
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
        'raffle_times' => 'array',
        'auto_create_raffles' => 'boolean',
        'data' => 'json',
    ];

    public $appends = [
        'qty_raffles_to_create',
    ];

    //----------
    // Finders
    //----------
    public static function findByCode($code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByName($name): ?self
    {
        return self::where('name', $name)->first();
    }

    //----------
    // Scopes
    //----------
    public function scopeAutoCR($query)
    {
        return $query->where('auto_create_raffles', true);
    }


    //------------
    // Mutators
    //------------
    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(substr($value, 0, 10));
    }

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }



    protected function setRaffleColorsAttribute($value)
    {
        $this->attributes['raffle_colors'] = strtoupper(substr($value, 0, 150));
    }

    protected function setColorAttribute($value)
    {
        $this->attributes['colour'] = substr($value, 0, 20);
    }

    protected function setImageAttribute($value)
    {
        $this->attributes['image'] = strtoupper(substr($value, 0, 200));
    }

    protected function setApiUrlAttribute($value)
    {
        $this->attributes['api_url'] = substr($value, 0, 200);
    }

    //-------------
    // Attributes
    //-------------
    protected function getQtyRafflesToCreateAttribute($value)
    {
        $qtyDailyRaffles = count($this->raffle_times);
        $qtyNextPeriodDays = 7;
        return $qtyNextPeriodDays * $qtyDailyRaffles;
    }

    protected function getTimesAttribute($value): array
    {
        $lotteryCode = $this->code;
        $lotteryTimes = $this->raffle_times;
        $arrayTimes = [];
        foreach ($lotteryTimes as $time) {
            $arrayTimes[] = $lotteryCode . '-' . $time;
        }
        return $arrayTimes;
    }



    //----------------
    // Relationships
    //----------------
    public function raffleTimesDependences(): HasMany
    {
        return $this->hasMany(RaffleTimesDependence::class);
    }

    public function lotteryGames(): HasMany
    {
        return $this->hasMany(LotteryGame::class);
    }

    // public function tickets(): HasMany
    // {
    //     return $this->hasMany(Ticket::class);
    // }

    public function raffles(): HasMany
    {
        return $this->hasMany(Raffle::class);
    }

}
