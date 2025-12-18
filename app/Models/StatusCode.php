<?php

namespace App\Models;

use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $code
 * @property string $type
 * @property string $description
 * @property bool $is_checked
 * @property string|null $color
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApiEvent> $apiEvents
 * @property-read int|null $api_events_count
 * @method static \Database\Factories\StatusCodeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatusCode meansFinished()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatusCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatusCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatusCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StatusCode unchecked()
 * @mixin \Eloquent
 */
class StatusCode extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;

    protected $table = "sports.status_codes";

    protected $fillable = [
        'code',
        'type',
        'description',
        'is_checked',
        'color',
        'image',
    ];


    protected function casts(): array
    {
        return [
            'id'             => 'integer',
            'is_checked'     => 'boolean',
        ];
    }

    public static function preset(): array
    {
        return [
            'notstarted' => [
                'color' => 'warning',
                'image' => 'heroicon-o-clock',
            ],
            'inprogress' => [
                'color' => 'info',
                'image' => 'heroicon-o-truck',
            ],
            'finished' => [
                'color' => 'success',
                'image' => 'heroicon-o-check',
            ],
        ];
    }

    //----------
    // Finders
    //----------
    public static function findByCode(int $code): ?self
    {
        return self::where('code', $code)->first();
    }


    //-----------------
    // Scopes
    //-----------------
    public function scopeMeansFinished($query)
    {
        return $query->where('type', 'finished');
    }

    public function scopeUnchecked($query)
    {
        return $query->where('is_checked', false);
    }


    //-----------
    // Mutators
    //-----------
    protected function setTypeAttribute($value)
    {
        $this->attributes['type'] = length($value,50);
    }

    protected function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = length($value,100);
    }

    protected function setImageAttribute($value)
    {
        $this->attributes['image'] = length($value,50);
    }

    protected function setColorAttribute($value)
    {
        $this->attributes['color'] = length($value,25);
    }


    //-----------------
    // Relationships
    //-----------------
    public function apiEvents(): HasMany
    {
        return $this->hasMany(ApiEvent::class);
    }
}
