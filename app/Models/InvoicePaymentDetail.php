<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $invoice_payment_id
 * @property int $invoice_id
 * @property int $amount
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\InvoicePayment $invoicePayment
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Database\Factories\InvoicePaymentDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereInvoicePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoicePaymentDetail whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class InvoicePaymentDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_payment_id',
        'invoice_id',
        'amount',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoice_payment_id' => 'integer',
        'invoice_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


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

    //----------------
    // Relationships
    //----------------
    public function invoicePayment(): BelongsTo
    {
        return $this->belongsTo(InvoicePayment::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
