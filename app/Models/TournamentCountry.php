<?php

namespace App\Models;

use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property bool $is_active
 * @property string $code
 * @property string $name
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry active()
 * @method static \Database\Factories\TournamentCountryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentCountry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TournamentCountry extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;

    protected $table = 'sports.tournament_countries';

    protected $fillable = [
        'is_active',
        'name',
        'code',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
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

    //----------
    // Scopes
    //----------


    //------------
    // Mutators
    //------------
    protected function setCodeAttribute($value)
    {
        $this->attributes['code'] = up($value, 3);
    }

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = up($value, 50);
    }

}
