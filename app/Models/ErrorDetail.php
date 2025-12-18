<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $process_id
 * @property string $reference
 * @property string $message
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Process $process
 * @property-write mixed $comments
 * @property-write mixed $error_messages
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail byProcess(int $processId)
 * @method static \Database\Factories\ErrorDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail whereProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ErrorDetail extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'error_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'process_id',
        'reference',
        'message',
        'comment',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'process_id' => 'integer',
    ];

    //----------
    // Finders
    //----------


    //----------
    // Scopes
    //----------
    public function scopeByProcess($query, int $processId)
    {
        return $query->where('process_id', $processId);
    }



    //------------
    // Mutators
    //------------
    protected function setMessageAttribute($value)
    {
        $this->attributes['message'] = up($value,50);
    }

    protected function setCommentAttribute($value)
    {
        $this->attributes['comment'] = up($value,50);
    }

    //-----------------
    // Other methods
    //-----------------
    public static function countByProcess(int $processId): int
    {
        return self::where('process_id', $processId)->count();
    }


    //---------------
    // Relationships
    //---------------
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

}
