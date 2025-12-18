<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\InvoicePayment\InvoicePaymentStatusEnum;

/**
 * @property int $id
 * @property int $consortium_id
 * @property int $invoice_payment_method_id
 * @property int $created_by
 * @property int $amount
 * @property \Illuminate\Support\Carbon $paid_at
 * @property string $reference
 * @property string $document
 * @property InvoicePaymentStatusEnum $payment_status
 * @property int|null $updated_by
 * @property int|null $excess_amount
 * @property string|null $bank
 * @property string|null $comments
 * @property string|null $rejected_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Consortium $consortium
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoicePaymentDetail> $invoicePaymentDetails
 * @property-read int|null $invoice_payment_details_count
 * @property-read \App\Models\InvoicePaymentMethod $invoicePaymentMethod
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment byConsortium(int $consortiumId)
 * @method static \Database\Factories\InvoicePaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereExcessAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereInvoicePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePayment whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class InvoicePayment extends Model implements \OwenIt\Auditing\Contracts\Auditable
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
        'reference',
        'invoice_payment_method_id',
        'document',
        'amount',
        'paid_at',
        'payment_status',
        'excess_amount',
        'bank',
        'comments',
        'created_by',
        'updated_by',
        'rejected_reason',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                        => 'integer',
        'created_by'                => 'integer',
        'updated_by'                => 'integer',
        'consortium_id'             => 'integer',
        'invoice_payment_method_id' => 'integer',
        'payment_status'            => InvoicePaymentStatusEnum::class,
        'amount'                    => 'integer',
        'excess_amount'             => 'integer',
        'paid_at'                   => 'date',
    ];


    //-----------
    // Scopes
    //-----------
    public function scopeByConsortium($query, int $consortiumId)
    {
        return $query->where('consortium_id', $consortiumId);
    }

    //-----------
    // Mutators
    //-----------
    protected function setDocumentAttribute($value)
    {
        $this->attributes['document'] = strtoupper(substr($value,0,50));
    }

    protected function setBankAttribute($value)
    {
        $this->attributes['bank'] = strtoupper(substr($value,0,50));
    }

    protected function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = strtoupper(substr($value,0,250));
    }

    protected function setRejectedReasonAttribute($value)
    {
        $this->attributes['rejected_reason'] = strtoupper(substr($value,0,250));
    }



    //-------------
    // Attributes
    //-------------
    protected function amount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }

    protected function excessAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //------------------
    // Relationships
    //------------------
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function invoicePaymentMethod(): BelongsTo
    {
        return $this->belongsTo(InvoicePaymentMethod::class);
    }

    public function invoicePaymentDetails(): HasMany
    {
        return $this->hasMany(InvoicePaymentDetail::class);
    }
}
