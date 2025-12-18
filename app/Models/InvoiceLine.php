<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $invoice_id
 * @property int|null $invoice_concept_id
 * @property string $description
 * @property int $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\InvoiceConcept|null $invoiceConcept
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine byInvoice(int $invoiceId)
 * @method static \Database\Factories\InvoiceLineFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine whereInvoiceConceptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceLine whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoiceLine extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'invoice_concept_id',
        'description',
        'amount',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoice_id' => 'integer',
        'invoice_concept_id' => 'integer',
    ];


    //---------
    // Scopes
    //---------
    public function scopeByInvoice($query, int $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    //-----------
    // Mutators
    //-----------
    protected function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = strtoupper(substr($value,0,100));
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


    // ---------------
    // Relationships
    // ---------------
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoiceConcept(): BelongsTo
    {
        return $this->belongsTo(InvoiceConcept::class);
    }
}
