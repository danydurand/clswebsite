<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $id
 * @property int|null $consortium_id
 * @property int|null $bank_id
 * @property string|null $date
 * @property string|null $lottery
 * @property int|null $sale
 * @property int|null $awards
 * @property-read mixed $award
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Consortium|null $consortium
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery whereAwards($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery whereLottery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SaleByLottery whereSale($value)
 * @mixin \Eloquent
 */
class SaleByLottery extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'lottery.sales_by_lottery_view';

    protected $casts = [
        'id'            => 'integer',
        'consortium_id' => 'integer',
        'bank_id'       => 'integer',
    ];



    //----------
    // Scopes
    //----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeByBank($query, int $bankId)
    {
        return $query->where('bank_id', $bankId);
    }


    //-------------
    // Attributes
    //-------------

    protected function sale(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function award(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

}
