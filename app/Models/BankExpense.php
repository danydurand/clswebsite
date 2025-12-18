<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\BankExpense\ExpenseStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $bank_id
 * @property int $concept_id
 * @property int $amount
 * @property \Illuminate\Support\Carbon $payment_date
 * @property ExpenseStatusEnum $status
 * @property int|null $route_id
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank $bank
 * @property-read \App\Models\Concept $concept
 * @property-read \App\Models\Route|null $route
 * @method static \Database\Factories\BankExpenseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereConceptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankExpense whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BankExpense extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'concept_id',
        'amount',
        'payment_date',
        'status',
        'route_id',
        'comments',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'bank_id'      => 'integer',
        'concept_id'   => 'integer',
        'payment_date' => 'date',
        'route_id'     => 'integer',
        'status'       => ExpenseStatusEnum::class,
    ];


    protected function amount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //-----------------
    // Relationships
    //-----------------
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(Concept::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
}
