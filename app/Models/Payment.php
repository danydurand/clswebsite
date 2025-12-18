<?php

namespace App\Models;

use App\Services\AuthUser;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $consortium_id
 * @property int|null $banker_id
 * @property string $name
 * @property bool $is_active
 * @property string|null $explanation
 * @property string|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Banker|null $banker
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \App\Models\Consortium $consortium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentDetail> $details
 * @property-read int|null $details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Seller> $sellers
 * @property-read int|null $sellers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Terminal> $terminals
 * @property-read int|null $terminals_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment byConsortium(int $consortiumId)
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereBankerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasRelatedRecords;
    use HandleActive;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consortium_id',
        'banker_id',
        'name',
        'is_active',
        'explanation',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'consortium_id' => 'integer',
        'is_active' => 'boolean',
        'banker_id' => 'integer',
    ];


    //----------
    // Finders
    //----------
    public static function findByConsortiumAndName(int $consortiumId, string $name): ?self
    {
        return self::where('consortium_id', $consortiumId)
            ->where('name', $name)
            ->first();
    }

    //---------
    // Scopes
    //---------
    public function scopeByConsortium($query, ?int $consortiumId)
    {
        return $consortiumId !== null ? $query->where('consortium_id', $consortiumId) : $query;
    }

    //-----------
    // Mutators
    //-----------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setExplanationAttribute($value)
    {
        $this->attributes['explanation'] = substr($value, 0, 200);
    }

    //------------------
    // Relationships
    //------------------
    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }

    public function banker(): BelongsTo
    {
        return $this->belongsTo(Banker::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PaymentDetail::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class);
    }
}
