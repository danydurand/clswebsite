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
 * @property int|null $qty
 * @property int|null $sold
 * @property int|null $prize
 * @property int|null $commission
 * @property int|null $profit
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\Supervisor|null $supervisor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 byGroup(int $groupId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 bySupervisor(int $supervisorId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 wherePrize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereSold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CashSummary2 whereSupervisorId($value)
 * @mixin \Eloquent
 */
class CashSummary2 extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'lottery.cash_summary2_view';

    protected $casts = [
        'id'            => 'integer',
        'consortium_id' => 'integer',
        'supervisor_id' => 'integer',
        'group_id'      => 'integer',
        'bank_id'       => 'integer',
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

    //-------------
    // Attributes
    //-------------

    protected function sold(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function commission(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function prize(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function profit(): Attribute
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

}
