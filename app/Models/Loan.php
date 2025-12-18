<?php

namespace App\Models;

use App\Domain\Loan\LoanStatusEnum;
use App\Domain\Loan\LoanFrequencyEnum;
use Illuminate\Database\Eloquent\Model;
use App\Domain\LoanDetail\FeeStatusEnum;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $bank_id
 * @property int $amount
 * @property int $qty_fees
 * @property int $percentage
 * @property LoanFrequencyEnum $frequency
 * @property int $fee_amount
 * @property LoanStatusEnum $status
 * @property int|null $route_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank $bank
 * @property-read mixed $fees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanDetail> $loanDetails
 * @property-read int|null $loan_details_count
 * @property-read mixed $paid_fees_count
 * @property-read mixed $pending_fees_count
 * @property-read \App\Models\Route|null $route
 * @method static \Database\Factories\LoanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereFeeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereQtyFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Loan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Loan extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'amount',
        'qty_fees',
        'percentage',
        'fee_amount',
        'frequency',
        'status',
        'route_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'route_id' => 'integer',
        'qty_fees' => 'integer',
        'bank_id' => 'integer',
        'frequency' => LoanFrequencyEnum::class,
        'status' => LoanStatusEnum::class,
    ];

    public $appends = [
        'fees_count',
        'pending_fees_count',
        'paid_fees_count'
    ];



    //------------------
    // Attributes
    //------------------
    protected function amount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function percentage(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function feeAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    // protected function qtyFees(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn ($value) => bcmul($value, 100, 0),
    //         get: fn ($value) => bcdiv($value, 100, 2)
    //     );
    // }

    protected function feesCount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->loanDetails()->count()
        );
    }

    protected function paidFeesCount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->loanDetails()
                    ->where('status', FeeStatusEnum::Paid->value)
                    ->count()
        );
    }

    protected function pendingFeesCount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->loanDetails()
                    ->where('status', FeeStatusEnum::Pending->value)
                    ->count()
        );
    }


    //-----------------
    // Relationships
    //-----------------
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

}
