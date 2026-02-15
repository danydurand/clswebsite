<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;

    protected $table = 'faq_view';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order',
        'type',
        'is_active',
        'title',
        'content',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];


    //-----------
    // Finders 
    //-----------
    public static function findByTitle(string $title): ?self
    {
        return self::where('title', $title)->first();
    }


    //-----------
    // Mutators 
    //-----------
    protected function setTypeAttribute($value)
    {
        $this->attributes['type'] = length($value, 25);
    }

    protected function setTitleAttribute($value)
    {
        $this->attributes['title'] = length($value, 100);
    }


}
