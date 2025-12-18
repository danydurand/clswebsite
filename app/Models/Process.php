<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $init_time
 * @property int $created_by
 * @property string|null $end_time
 * @property int|null $processed_records
 * @property int $qty_errors
 * @property string|null $time_consumed
 * @property bool|null $notify_admin
 * @property bool|null $notify_user
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ErrorDetail> $errorDetails
 * @property-read int|null $error_details_count
 * @method static \Database\Factories\ProcessFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereInitTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereNotifyAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereNotifyUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereProcessedRecords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereQtyErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereTimeConsumed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Process withErrors()
 * @mixin \Eloquent
 */
class Process extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'processes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'init_time',
        'end_time',
        'processed_records',
        'qty_errors',
        'time_consumed',
        'comments',
        'created_by',
        'notify_admin',
        'notify_user',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'created_by'   => 'integer',
        'notify_admin' => 'boolean',
        'notify_user'  => 'boolean',
    ];

    //----------
    // Finders
    //----------
    public static function findByNameAndInitTime(string $name, string $initTime)
    {
        return self::where('name', $name)
                    ->where('init_time', $initTime)
                    ->first();
    }

    //----------
    // Scopes
    //----------
    public function scopeWithErrors($query)
    {
        return $query->where('qty_errors', '>', 0);
    }



    //------------
    // Mutators
    //------------
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value,0,100));
    }

    protected function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = strtoupper($value);
    }

    //----------------
    // Relationships
    //----------------
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function errorDetails(): HasMany
    {
        return $this->hasMany(ErrorDetail::class);
    }


}
