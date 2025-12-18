<?php

namespace App\Models;

use App\Domain\Post\PostLayoutEnum;
use App\Models\Traits\HandleActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $section_id
 * @property bool $is_active
 * @property string $layout
 * @property string $title
 * @property string $sub_title
 * @property string $content
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $width
 * @property int|null $height
 * @property string|null $route
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PostContent> $postContents
 * @property-read int|null $post_contents_count
 * @property-read \App\Models\Section $section
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post active()
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @mixin \Eloquent
 */
class Post extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HandleActive;
    use HasRelatedRecords;


    protected $fillable = [
        'section_id',
        'is_active',
        'title',
        'route',
    ];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'section_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }


    //-----------
    // Finders
    //-----------
    public static function findBySectionAndTitle(int $sectionId, string $title): ?Post
    {
        return self::where('section_id', $sectionId)
            ->where('title', $title)
            ->first();
    }


    //------------
    // Mutators
    //------------
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = length($value, 100);
    }

    public function setSubTitleAttribute(string $value): void
    {
        $this->attributes['sub_title'] = length($value, 100);
    }

    public function setRouteAttribute(string $value): void
    {
        $this->attributes['route'] = length($value, 100);
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
    public function section(): BelongsTo
    {
        // This value is set in middleware App\Http\Middleware\SetSystemContext
        $systemId = session('system_id');

        return $this->belongsTo(Section::class)
            ->where('system_id', $systemId)
            ->orderBy('position');
    }

    public function postContents(): HasMany
    {
        return $this->hasMany(PostContent::class);
    }


}
