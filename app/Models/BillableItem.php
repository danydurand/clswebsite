<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $consortium_id
 * @property int $bank_id
 * @property int $item_id
 * @property string $item_type
 * @property int $billable_year
 * @property int $billable_month
 * @property int $days_of_sale
 * @property int|null $invoice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank $bank
 * @property-read \App\Models\Consortium $consortium
 * @property-read \App\Models\Invoice|null $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem byYearMonth(int $year, int $month)
 * @method static \Database\Factories\BillableItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereBillableMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereBillableYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereDaysOfSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillableItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BillableItem extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'bank_id',
        'item_id',
        'item_type',
        'billable_year',
        'billable_month',
        'days_of_sale',
        'invoice_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'consortium_id' => 'integer',
        'bank_id' => 'integer',
        'invoice_id' => 'integer',
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

    public function scopeByYearMonth($query, int $year, int $month)
    {
        return $query->where('billable_year', $year)->where('billable_month', $month);
    }


    //-----------------
    // Relationships
    //-----------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
