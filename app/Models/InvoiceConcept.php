<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property int $order
 * @property string $showing_description
 * @property bool $is_active
 * @property bool $is_fix
 * @property string $operation
 * @property string $how_it_does_apply
 * @property string $type
 * @property int $country_id
 * @property string $data
 * @property string $value
 * @property string $min_value
 * @property string|null $applies_to
 * @property \Illuminate\Support\Carbon|null $begin_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property int|null $tax_base
 * @property string|null $method_name
 * @property string|null $condition
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept byCountry(int $countryId)
 * @method static \Database\Factories\InvoiceConceptFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept fix()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereAppliesTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereBeginDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereHowItDoesApply($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereIsFix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereMethodName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereMinValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereShowingDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereTaxBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceConcept whereValue($value)
 * @mixin \Eloquent
 */
class InvoiceConcept extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'order',
        'showing_description',
        'is_active',
        'is_fix',
        'operation',
        'how_it_does_apply',
        'type',
        'applies_to',
        'country_id',
        'data',
        'value',
        'min_value',
        'begin_date',
        'end_date',
        'tax_base',
        'method_name',
        'condition',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'is_fix' => 'boolean',
        'country_id' => 'integer',
        'begin_date' => 'date',
        'end_date' => 'date',
    ];



    //----------
    // Finders
    //----------
    public static function findByNameAndCountry(string $name, int $countryId): ?self
    {
        return self::where('name', $name)
            ->where('country_id', $countryId)
            ->first();
    }


    //-----------
    // Scopes
    //-----------
    public function scopeFix($query)
    {
        return $query->where('is_fix', true);
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
        $this->attributes['name'] = strtoupper(substr($value, 0, 50));
    }

    protected function setShowingDescriptionAttribute($value)
    {
        $this->attributes['showing_description'] = ucfirst(substr($value, 0, 100));
    }


    //----------------
    // Relationships
    //----------------
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
