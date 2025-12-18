<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\CreditNote\CreditNoteTypeEnum;
use App\Domain\CreditNote\CreditNoteStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $consortium_id
 * @property int|null $customer_id
 * @property int $amount
 * @property int $used_amount
 * @property string $reference
 * @property CreditNoteTypeEnum $type
 * @property CreditNoteStatusEnum $status
 * @property int|null $invoice_payment_id
 * @property string $description
 * @property string|null $how_was_used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $balance
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\InvoicePayment|null $invoicePayment
 * @method static \Database\Factories\CreditNoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereHowWasUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereInvoicePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditNote whereUsedAmount($value)
 * @mixin \Eloquent
 */
class CreditNote extends Model implements \OwenIt\Auditing\Contracts\Auditable
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
        'customer_id',
        'reference',
        'type',
        'amount',
        'used_amount',
        'status',
        'invoice_payment_id',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                 => 'integer',
        'consortium_id'      => 'integer',
        'customer_id'        => 'integer',
        'invoice_payment_id' => 'integer',
        'status'             => CreditNoteStatusEnum::class,
        'type'               => CreditNoteTypeEnum::class,
    ];

    //----------
    // Finders
    //----------
    public static function findByReference(string $reference): ?self
    {
        return self::where('reference', $reference)->first();
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

    protected function usedAmount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcmul($value, 100, 0),
            get: fn ($value) => bcdiv($value, 100, 2)
        );
    }


    //----------------
    // Relationships
    //----------------
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function invoicePayment(): BelongsTo
    {
        return $this->belongsTo(InvoicePayment::class);
    }
}
