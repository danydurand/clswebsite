<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermAndCondition extends Model
{
    use HasFactory;
    use HandleActive;

    protected $fillable = [
        'order',
        'system_id',
        'is_active',
        'text',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'order' => 'integer',
            'system_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    //---------
    // Scopes 
    //---------
    public function scopeBySystem($query, int $systemId)
    {
        return $query->where('system_id', $systemId)
            ->orderBy('order');
    }



    //----------------
    // Relationships
    //----------------
    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }


}
