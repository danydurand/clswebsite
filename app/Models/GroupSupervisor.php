<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\User|null $supervisor
 * @method static \Database\Factories\GroupSupervisorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSupervisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSupervisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupSupervisor query()
 * @mixin \Eloquent
 */
class GroupSupervisor extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'supervisor_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'group_id'      => 'integer',
        'supervisor_id' => 'integer',
    ];


    // -----------------
    // Relationships
    // -----------------
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
