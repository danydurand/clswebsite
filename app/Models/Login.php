<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $entity_id
 * @property string $entity_type
 * @property string $ip_address
 * @property string $city
 * @property string $country
 * @property string $country_code
 * @property string $browser
 * @property string $os
 * @property string|null $longitud
 * @property string|null $latitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\LoginFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Login extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'lottery.logins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_type',
        'ip_address',
        'city',
        'country',
        'country_code',
        'browser',
        'os',
        'longitud',
        'latitude',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'entity_id' => 'integer',
    ];


    //------------
    // Mutators
    //------------
    protected function setBrowserAttribute($value)
    {
        $this->attributes['browser'] = ucfirst(substr($value,0,50));
    }

    protected function setCountryCodeAttribute($value)
    {
        $this->attributes['country_code'] = strtoupper(substr($value,0,10));
    }

    protected function setOsAttribute($value)
    {
        $this->attributes['os'] = ucfirst(substr($value,0,50));
    }

    protected function setCountryAttribute($value)
    {
        $this->attributes['country'] = ucfirst(substr($value,0,50));
    }

}
