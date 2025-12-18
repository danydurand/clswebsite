<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use App\Domain\Invoice\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $consortium_id
 * @property string $total_amount
 * @property string $paid_amount
 * @property int $year
 * @property int $month
 * @property string $reference
 * @property string $business_number
 * @property string $business_name
 * @property string $business_address
 * @property string $business_phone
 * @property \App\Domain\Invoice\InvoiceStatusEnum $invoice_status
 * @property int $invoice_payment_condition_id
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $totally_paid_at
 * @property bool $is_sent_by_email
 * @property \Carbon\Carbon|null $sent_by_email_at
 * @property int $country_id
 * @property int|null $nullified_by
 * @property string|null $nullified_motive
 * @property \Carbon\Carbon|null $nullified_at
 * @property int|null $credit_note_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read string $pending_amount
 * @property array<array-key, mixed>|null $data
 * @property-read \App\Models\Consortium $consortium
 * @property-read \App\Models\Country|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CreditNote> $creditNotes
 * @property-read int|null $credit_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceLine> $invoiceLines
 * @property-read int|null $invoice_lines_count
 * @property-read \App\Models\InvoicePaymentCondition $invoicePaymentCondition
 * @property-read \App\Models\User|null $nullifiedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice byConsortium($consortiumId)
 * @method static \Database\Factories\InvoiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBusinessAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBusinessNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBusinessPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreditNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoicePaymentConditionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIsSentByEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNullifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNullifiedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNullifiedMotive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSentByEmailAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotallyPaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereYear($value)
 * @mixin \Eloquent
 */
class Invoice extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;
    use JsonData;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'total_amount',
        'paid_amount',
        'year',
        'month',
        'reference',
        'business_number',
        'business_name',
        'business_address',
        'business_phone',
        'invoice_status',
        'invoice_payment_condition_id',
        'due_date',
        'totally_paid_at',
        'is_sent_by_email',
        'sent_by_email_at',
        'country_id',
        'nullified_by',
        'nullified_motive',
        'nullified_at',
        'credit_note_id',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                           => 'integer',
        'consortium_id'                => 'integer',
        'year'                         => 'integer',
        'month'                        => 'integer',
        'invoice_status'               => InvoiceStatusEnum::class,
        'invoice_payment_condition_id' => 'integer',
        'due_date'                     => 'date',
        'totally_paid_at'              => 'datetime',
        'is_sent_by_email'             => 'boolean',
        'sent_by_email_at'             => 'datetime',
        'country_id'                   => 'integer',
        'nullified_by'                 => 'integer',
        'nullified_at'                 => 'datetime',
        'data'                         => 'json',
    ];


    public $appends = [
        'pending_amount',
    ];

    //----------
    // Finders
    //----------
    public static function findByReference(string $reference): ?self
    {
        return self::where('reference', $reference)->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeByConsortium($query, $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    public function scopePending($query)
    {
        return $query->whereIn('invoice_status', [
            InvoiceStatusEnum::Pending->value,
            InvoiceStatusEnum::PartiallyPaid->value,
        ]);
    }


    //-----------
    // Mutators
    //-----------
    protected function setBusinessNumberAttribute($value)
    {
        $this->attributes['business_number'] = strtoupper(substr($value,0,30));
    }

    protected function setBusinessNameAttribute($value)
    {
        $this->attributes['business_name'] = strtoupper(substr($value,0,100));
    }

    protected function setBusinessAddressAttribute($value)
    {
        $this->attributes['business_address'] = strtoupper(substr($value,0,250));
    }

    protected function setBusinessPhoneAttribute($value)
    {
        $this->attributes['business_phone'] = strtoupper(substr($value,0,50));
    }

    protected function setNullifiedMotiveAttribute($value)
    {
        $this->attributes['nullified_motive'] = strtoupper(substr($value,0,100));
    }


    //-------------
    // Attributes
    //-------------
    protected function pendingAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => bcsub($this->total_amount, $this->paid_amount, 2) > 0
                                        ? bcsub($this->total_amount, $this->paid_amount, 2)
                                        : 0
        );
    }

    protected function totalAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function paidAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    //------------------
    // Relationships
    //------------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function invoicePaymentCondition(): BelongsTo
    {
        return $this->belongsTo(InvoicePaymentCondition::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function nullifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nullified_by');
    }

    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }
}
