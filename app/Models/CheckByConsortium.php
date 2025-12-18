<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $id
 * @property int|null $consortium_id
 * @property int|null $supervisor_id
 * @property int|null $group_id
 * @property int|null $bank_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property int|null $seller_id
 * @property int|null $qty
 * @property int|null $sold
 * @property int|null $prize
 * @property int|null $commission
 * @property int|null $profit
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\Seller|null $seller
 * @property-read \App\Models\Supervisor|null $supervisor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary byGroup(int $groupId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary bySeller(int $sellerId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary bySupervisor(int $supervisorId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary wherePrize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereSold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary whereSupervisorId($value)
 * @mixin \Eloquent
 */
class CheckByConsortium extends Model
{

    protected $table = 'lottery.checks_by_consortium_view';

    protected $casts = [
        'consortium_id' => 'integer',
        'supervisor_id' => 'integer',
        'group_id' => 'integer',
        'bank_id' => 'integer',
        'seller_id' => 'integer',
        'created_at' => 'datetime',
        'stake_amount' => 'integer',
        'return_amount' => 'integer',
        'profit' => 'integer',
    ];



    //----------
    // Scopes
    //----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopeBySupervisor($query, int $supervisorId)
    {
        // $groupIds = Group::where('supervisor_id', $supervisorId)->pluck('id');
        return $query->where('supervisor_id', $supervisorId);
    }

    public function scopeByGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeByBank($query, int $bankId)
    {
        return $query->where('bank_id', $bankId);
    }

    public function scopeBySeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }


    //-------------
    // Attributes
    //-------------

    protected function stakeAmount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function returnAmount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function profit(): Attribute
    {
        return Attribute::make(
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

}
