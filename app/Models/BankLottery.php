<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HandleActive;

/**
 * @property int $id
 * @property int $bank_id
 * @property int $lottery_id
 * @property string $raffle_time
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank $bank
 * @property-read \App\Models\Lottery $lottery
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery byBank(int $bankId)
 * @method static \Database\Factories\BankLotteryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery whereLotteryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery whereRaffleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankLottery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BankLottery extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, HandleActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'lottery_id',
        'raffle_time',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'bank_id' => 'integer',
        'lottery_id' => 'integer',
        'is_active' => 'boolean',
    ];


    //----------
    // Finders 
    //----------
    public static function findByBankLotteryTime(int $bankId, int $lotteryId, string $time): ?self
    {
        return self::where('bank_id', $bankId)
            ->where('lottery_id', $lotteryId)
            ->where('raffle_time', $time)
            ->first();
    }

    //----------
    // Scopes 
    //----------
    public function scopeByBank($query, int $bankId)
    {
        return $query->where('bank_id', $bankId);
    }

    //----------------
    // Relationships 
    //----------------
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }
}
