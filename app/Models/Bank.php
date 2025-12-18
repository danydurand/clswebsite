<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $consortium_id
 * @property int $group_id
 * @property bool $is_active
 * @property string $code
 * @property string $name
 * @property string $contact_name
 * @property string $email
 * @property int|null $restriction_id
 * @property string|null $restriction_assigned_at
 * @property int|null $commission_id
 * @property string|null $commission_assigned_at
 * @property int|null $payment_id
 * @property string|null $payment_assigned_at
 * @property int|null $limit_id
 * @property string|null $limit_assigned_at
 * @property string|null $last_activity_at
 * @property bool|null $without_activity
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $zipcode
 * @property string|null $document_id
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Commission|null $commission
 * @property-read \App\Models\Consortium $consortium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankExpense> $expenses
 * @property-read int|null $expenses_count
 * @property-read mixed $is_banker
 * @property-read \App\Models\Group $group
 * @property-read \App\Models\Group|null $groupOfTheConsortium
 * @property-read \App\Models\Limit|null $limit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanDetail> $loanDetails
 * @property-read int|null $loan_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Loan> $loans
 * @property-read int|null $loans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankLottery> $lotteries
 * @property-read int|null $lotteries_count
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Restriction|null $restriction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $sellers
 * @property-read int|null $sellers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $staff
 * @property-read int|null $staff_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Terminal> $terminals
 * @property-read int|null $terminals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank byConsortium(int $consortiumId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank byGroup(int $groupId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank byRestriction(int $restrictionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank bySupervisor(int $supervisorId)
 * @method static \Database\Factories\BankFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCommissionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCommissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereLimitAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereLimitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank wherePaymentAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereRestrictionAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereRestrictionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereWithoutActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereZipcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank withoutActivity()
 * @mixin \Eloquent
 */
class Bank extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, JsonData, HandleActive;
    use HasRelatedRecords;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'group_id',
        'is_active',
        'code',
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'zipcode',
        'document_id',
        'restriction_id',
        'restriction_assigned_at',
        'commission_id',
        'commission_assigned_at',
        'payment_id',
        'payment_assigned_at',
        'limit_id',
        'limit_assigned_at',
        'last_activity_at',
        'without_activity',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'consortium_id' => 'integer',
        'group_id' => 'integer',
        'is_active' => 'boolean',
        'without_activity' => 'boolean',
        'restriction_id' => 'integer',
        'commission_id' => 'integer',
        'payment_id' => 'integer',
        'limit_id' => 'integer',
    ];


    //----------
    // Finders
    //----------
    public static function findByGroupAndName(int $groupId, string $name): ?self
    {
        return self::where('group_id', $groupId)
            ->where('name', $name)
            ->first();
    }

    public static function findByGroupAndCode(int $groupId, string $code): ?self
    {
        return self::where('group_id', $groupId)
            ->where('code', $code)
            ->first();
    }


    //---------
    // Scopes
    //---------
    public function scopeWithoutActivity($query)
    {
        return $query->where('without_activity', true);
    }

    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('banks.consortium_id', $consortiumId);
    }

    public function scopeByGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeBySupervisor($query, int $supervisorId)
    {
        return $query->whereRelation('group', 'supervisor_id', $supervisorId);
    }

    public function scopeByRestriction($query, int $restrictionId)
    {
        return $query->where('restriction_id', $restrictionId);
    }


    //-----------
    // Mutators
    //-----------
    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(substr($value, 0, 10));
    }

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setContactNameAttribute($value)
    {
        $this->attributes['contact_name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(substr($value, 0, 100));
    }

    protected function setAddressAttribute($value)
    {
        $this->attributes['address'] = strtoupper(substr($value, 0, 250));
    }

    protected function setZipcodeAttribute($value)
    {
        $this->attributes['zipcode'] = substr($value, 0, 10);
    }

    //-------------
    // Attributes
    //-------------
    public function getIsBankerAttribute($value)
    {
        return $this->group->is_banker;
    }

    //-----------------
    // Relationships
    //-----------------
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function loanDetails(): HasManyThrough
    {
        return $this->hasManyThrough(LoanDetail::class, Loan::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(BankExpense::class);
    }

    public function limit(): BelongsTo
    {
        return $this->belongsTo(Limit::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function restriction(): BelongsTo
    {
        return $this->belongsTo(Restriction::class);
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(User::class, 'bank_id', 'id');
    }

    public function lotteries(): HasMany
    {
        return $this->hasMany(BankLottery::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'bank_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class);
    }

    public function groupOfTheConsortium(): BelongsTo
    {
        return $this->belongsTo(Group::class)
            ->where('consortium_id', $this->consortium_id);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
        // ->where('consortium_id', $this->consortium_id);
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }
}
