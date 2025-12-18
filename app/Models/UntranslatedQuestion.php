<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $category_id
 * @property string|null $title
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UntranslatedQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UntranslatedQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UntranslatedQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UntranslatedQuestion whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UntranslatedQuestion whereTitle($value)
 * @mixin \Eloquent
 */
class UntranslatedQuestion extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'sports.untranslated_questions_view';

}
