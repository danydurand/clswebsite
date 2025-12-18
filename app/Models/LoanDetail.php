<?php

namespace App\Models;

use App\Observers\LoanDetailObserver;
use Illuminate\Database\Eloquent\Model;
use App\Domain\LoanDetail\FeeStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([LoanDetailObserver::class])]
/**
 * @property int $id
 * @property int $loan_id
 * @property int $amount
 * @property \Illuminate\Support\Carbon $collection_date
 * @property int|null $route_id
 * @property FeeStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Loan $loan
 * @property-read \App\Models\Route|null $route
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail byLoan(int $loanId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail byRoute(int $routeId)
 * @method static \Database\Factories\LoanDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail late()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereCollectionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereLoanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoanDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_id',
        'amount',
        'collection_date',
        'route_id',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'loan_id' => 'integer',
        'collection_date' => 'date',
        'route_id' => 'integer',
        'status' => FeeStatusEnum::class,
    ];


    //---------
    // Scopes
    //---------
    public function scopeByLoan($query, int $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    public function scopeByRoute($query, int $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    public function scopePending($query)
    {
        return $query->where('status', FeeStatusEnum::Pending->value);
    }

    public function scopePaid($query)
    {
        return $query->where('status', FeeStatusEnum::Paid->value);
    }

    public function scopeLate($query)
    {
        return $query->where('status', FeeStatusEnum::Late->value);
    }

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

    //----------------
    // Relationships
    //----------------
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
}
