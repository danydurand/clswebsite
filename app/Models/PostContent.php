<?php

namespace App\Models;

use App\Domain\Post\PostLayoutEnum;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_id
 * @property bool $is_active
 * @property PostLayoutEnum $layout
 * @property string $sub_title
 * @property string $content
 * @property string|null $image
 * @property string|null $width
 * @property string|null $height
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Post $post
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostContent active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostContent active()
 * @method static \Database\Factories\PostContentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostContent query()
 * @mixin \Eloquent
 */
class PostContent extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;

    protected $fillable = [
        'post_id',
        'is_active',
        'layout',
        'sub_title',
        'content',
        'image',
        'width',
        'height',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'post_id' => 'integer',
            'is_active' => 'boolean',
            'layout' => PostLayoutEnum::class,
        ];
    }


    //-----------
    // Finders
    //-----------
    public static function findByPostAndSubtitle(int $postId, string $subTitle): ?System
    {
        return self::where('post_id', $postId)
            ->where('sub_title', $subTitle)
            ->first();
    }


    //------------
    // Mutators
    //------------
    public function setSubTitleAttribute(string $value): void
    {
        $this->attributes['sub_title'] = length($value, 100);
    }

    public function setLayoutAttribute(string $value): void
    {
        $this->attributes['layout'] = length($value, 20);
    }

    public function setImageAttribute(?string $value): void
    {
        $this->attributes['image'] = $value === null ? '' : length($value, 100);
    }



    //----------------
    // Relationships
    //----------------
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

}
