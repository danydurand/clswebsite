<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\Traits\JsonData;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $country_id
 * @property int $rate_id
 * @property int $invoice_payment_condition_id
 * @property string $rate_assigned_at
 * @property int $total_debt
 * @property int $total_cred
 * @property string $business_number
 * @property string $name
 * @property string $code
 * @property bool $is_active
 * @property string $contact_name
 * @property string $email
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BillableItem> $billableItems
 * @property-read int|null $billable_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Commission> $commissions
 * @property-read int|null $commissions_count
 * @property-read \App\Models\Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CreditNote> $creditNotes
 * @property-read int|null $credit_notes_count
 * @property-read mixed $deleteable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \App\Models\InvoicePaymentCondition $invoicePaymentCondition
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoicePayment> $invoicePayments
 * @property-read int|null $invoice_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConsortiumLottery> $lotteries
 * @property-read int|null $lotteries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Rate $rate
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Restriction> $restrictions
 * @property-read int|null $restrictions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Scrutiny> $scrutinies
 * @property-read int|null $scrutinies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $staff
 * @property-read int|null $staff_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Supervisor> $supervisors
 * @property-read int|null $supervisors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium byCountry(int $countryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium byRate(int $rateId)
 * @method static \Database\Factories\ConsortiumFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereBusinessNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereInvoicePaymentConditionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereRateAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereRateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereTotalCred($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereTotalDebt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consortium whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Consortium extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, JsonData, HandleActive;
    use HasRelatedRecords;

    protected $table = 'consortiums';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'rate_id',
        'invoice_payment_condition_id',
        'total_debt',
        'total_cred',
        'rate_assigned_at',
        'name',
        'code',
        'is_active',
        'business_number',
        'address',
        'phone',
        'contact_name',
        'email',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'country_id' => 'integer',
        'invoice_payment_condition_id' => 'integer',
        'rate_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public $appends = [
        'deleteable',
    ];

    //----------
    // Finders
    //----------
    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    public static function findByBusinessNumber(string $businessNumber): ?self
    {
        return self::where('business_number', $businessNumber)->first();
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeByRate($query, int $rateId)
    {
        return $query->where('rate_id', $rateId);
    }

    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }


    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setBusinessNumberAttribute($value)
    {
        $this->attributes['business_number'] = strtoupper(substr($value, 0, 25));
    }

    protected function setContactNameAttribute($value)
    {
        $this->attributes['contact_name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(substr($value, 0, 10));
    }

    protected function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(substr($value, 0, 100));
    }

    protected function setAddressAttribute($value)
    {
        $this->attributes['address'] = strtoupper(substr($value, 0, 250));
    }

    protected function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = substr($value, 0, 25);
    }


    //-------------
    // Attributes
    //-------------
    protected function totalDebt(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function totalCred(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100, 0),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function deleteable(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $this->tickets()->count() === 0 && $this->invoices()->count() === 0;
            }
        );
    }

    //-----------------
    // Relationships
    //-----------------
    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function invoicePayments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function billableItems(): HasMany
    {
        return $this->hasMany(BillableItem::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scrutinies(): HasMany
    {
        return $this->hasMany(Scrutiny::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'consortium_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function lotteries(): HasMany
    {
        return $this->hasMany(ConsortiumLottery::class);
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    public function invoicePaymentCondition(): BelongsTo
    {
        return $this->belongsTo(InvoicePaymentCondition::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
