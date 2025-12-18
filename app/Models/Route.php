<?php

namespace App\Models;

use App\Domain\Transaction\TransactionDoneEnum;
use App\Models\Traits\HasRelatedRecords;
use App\Models\Traits\JsonData;
use App\Domain\Route\RouteStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $supervisor_id
 * @property string $name
 * @property \Illuminate\Support\Carbon $init_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property RouteStatusEnum|null $status
 * @property string|null $comments
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $done_transac_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 * @property-read int|null $loans_count
 * @property-read \App\Models\User $supervisor
 * @property-read mixed $transac_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read mixed $undone_transac_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route bySupervisor(int $supervisorId)
 * @method static \Database\Factories\RouteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereInitDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Route extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use JsonData;
    use HasRelatedRecords;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supervisor_id',
        'name',
        'init_date',
        'end_date',
        'status',
        'comments',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'supervisor_id' => 'integer',
        'init_date' => 'date',
        'end_date' => 'date',
        'status' => RouteStatusEnum::class,
    ];

    public $appends = [
        'transac_count',
        'done_transac_count',
        'undone_transac_count'
    ];


    //----------
    // Finders
    //----------
    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeBySupervisor($query, int $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value,0,20));
    }

    protected function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = strtoupper(substr($value,0,250));
    }


    protected function transacCount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->transactions()->count()
        );
    }

    protected function doneTransacCount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->transactions()
                    ->whereIn('done', [
                        TransactionDoneEnum::Yes->value,
                        TransactionDoneEnum::Forwarded->value,
                    ])
                    ->count()
        );
    }

    protected function undoneTransacCount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>
                $this->transactions()
                    ->where('done', TransactionDoneEnum::No->value)
                    ->count()
        );
    }


    //------------------
    // Relationships
    //------------------
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
