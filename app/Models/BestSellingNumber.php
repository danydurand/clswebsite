<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $id
 * @property int|null $consortium_id
 * @property int|null $supervisor_id
 * @property int|null $group_id
 * @property int|null $bank_id
 * @property string|null $date
 * @property string|null $lottery
 * @property string|null $game
 * @property string|null $bet
 * @property int|null $qty
 * @property int|null $stake_amount
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereBet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereLottery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereStakeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BestSellingNumber whereSupervisorId($value)
 * @mixin \Eloquent
 */
class BestSellingNumber extends Model
{
    protected $table = 'lottery.best_selling_numbers_view';
}
