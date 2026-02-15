<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'act',
        'form_data',
    ];


    //--------
    // Casts
    //--------
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'form_data' => 'array',
        ];
    }


    //-----------
    // Scopes
    //-----------
    public function scopeForDeposit($query)
    {
        return $query->where('act', 'deposit');
    }

    public function scopeForWithdraw($query)
    {
        return $query->where('act', 'withdraw');
    }


    //----------
    // Methods
    //----------

    /**
     * Get form fields
     */
    public function getFields(): array
    {
        return $this->form_data ?? [];
    }

    /**
     * Validate data against form definition
     */
    public function validateData(array $data): bool
    {
        $fields = $this->getFields();

        foreach ($fields as $field) {
            if ($field['required'] ?? false) {
                if (!isset($data[$field['name']]) || empty($data[$field['name']])) {
                    return false;
                }
            }
        }

        return true;
    }

    //-----------------
    // Relationships
    //-----------------

    public function gateways(): HasMany
    {
        return $this->hasMany(Gateway::class, 'form_id');
    }

    public function withdrawMethods(): HasMany
    {
        return $this->hasMany(WithdrawMethod::class, 'form_id');
    }


}
