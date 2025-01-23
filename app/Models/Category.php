<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property int $parent_id
 *
 * @property Collection<Recipe> $recipes
 * @property Category $parent
 */
class Category extends Model
{
    protected $fillable = [
        'title',
        'parent_id',
    ];

    public $timestamps = false;

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
