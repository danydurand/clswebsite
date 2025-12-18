<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Transaction\TransactionDoneEnum;
use App\Domain\Transaction\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * @property int $id
 * @property int $route_id
 * @property int $supervisor_id
 * @property int $group_id
 * @property int $bank_id
 * @property TransactionTypeEnum $type
 * @property float $debit_amount
 * @property float $credit_amount
 * @property float $real_amount
 * @property TransactionDoneEnum $done
 * @property string $description
 * @property bool $banker_approvement
 * @property bool $consortium_approvement
 * @property string $comments
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $transaction_date
 * @property-read mixed $amount
 * @property-read \App\Models\Bank $bank
 * @property-read \App\Models\Group $group
 * @property-read \App\Models\Route|null $route
 * @property-read \App\Models\User $supervisor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction byBank(int $bankId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction byGroup(int $groupId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction byRoute(int $routeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction bySupervisor(int $supervisorId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction date($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction done()
 * @method static \Database\Factories\TransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction type(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereBankerApprovement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereConsortiumApprovement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreditAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDebitAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereRealAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @property bool $banker_aprovement
 * @property bool $consortium_aprovement
 * @mixin \Eloquent
 */

#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $fillable = [
        'route_id',
        'supervisor_id',
        'group_id',
        'bank_id',
        'type',
        'debit_amount',
        'credit_amount',
        'real_amount',
        'transaction_date',
        'done',
        'description',
        'banker_approvement',
        'consortium_approvement',
        'comments',
    ];

    protected $casts = [
        'id'                     => 'integer',
        'route_id'               => 'integer',
        'supervisor_id'          => 'integer',
        'group_id'               => 'integer',
        'bank_id'                => 'integer',
        'done'                   => TransactionDoneEnum::class,
        'banker_approvement'     => 'boolean',
        'consortium_approvement' => 'boolean',
        'type'                   => TransactionTypeEnum::class,
    ];



    //---------
    // Scopes
    //---------
    public function scopeByRoute($query, int $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    public function scopeBySupervisor($query, int $supervisorId)
    {
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

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('done', 'no');
    }

    public function scopeDone($query)
    {
        return $query->where('done', 'yes');
    }

    public function scopeDate($query, $date)
    {
        return $query->where('transaction_date', $date);
    }

    //-----------
    // Mutators
    //-----------
    protected function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = strtoupper(substr($value,0,1000));
    }

    protected function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = ucfirst(substr($value,0,250));
    }

    //------------------
    // Attributes
    //------------------
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (TransactionTypeEnum::getSign()[$this->type->value] == 'DEBIT') {
                    return $this->debit_amount;
                } else {
                    return $this->credit_amount;
                }
            }
        );
    }

    protected function realAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function debitAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function creditAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    //----------------
    // Relationships
    //----------------
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
