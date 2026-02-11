<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gateway extends Model
{
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;

    //-------------
    // Fillable
    //-------------
    protected $fillable = [
        'code',
        'name',
        'alias',
        'is_active',
        'is_manual',
        'image',
        'is_crypto',
        'form_id',
        'input_form',
        'description',
        'gateway_parameters',
        'supported_currencies',
        'extra',
    ];


    //--------
    // Casts
    //--------
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'is_manual' => 'boolean',
            'is_crypto' => 'boolean',
            'form_id' => 'integer',
            'input_form' => 'array',
            'gateway_parameters' => 'array',
            'supported_currencies' => 'array',
            'extra' => 'array',
        ];
    }


    //----------
    // Finders
    //----------
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    public static function findByAlias(string $alias): ?self
    {
        return self::where('alias', $alias)->first();
    }


    //-----------
    // Scopes
    //-----------
    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('is_manual', false);
    }

    //-----------
    // Mutators
    //-----------
    protected function setCodeAttribute($value): void
    {
        $this->attributes['code'] = up($value, 20);
    }

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = up($value, 100);
    }

    protected function setAliasAttribute($value): void
    {
        $this->attributes['alias'] = up($value, 50);
    }

    protected function setImageAttribute($value): void
    {
        $this->attributes['image'] = length($value, 150);
    }


    //-----------------
    // Relationships
    //-----------------
    public function gatewayCurrencies(): HasMany
    {
        return $this->hasMany(GatewayCurrency::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }


    //----------
    // Methods
    //----------

    /**
     * Get form fields for this gateway
     * Priority: input_form (JSON) > form_id (reusable form)
     */
    public function getFormFields(): array
    {
        // Priority 1: input_form (JSON directo)
        if ($this->input_form) {
            return $this->input_form;
        }

        // Priority 2: form_id (formulario reutilizable)
        if ($this->form_id && $this->form) {
            return $this->form->getFields();
        }

        return [];
    }

    /**
     * Check if this is a manual gateway
     */
    public function isManual(): bool
    {
        return $this->is_manual === true;
    }
}
