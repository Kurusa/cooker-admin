<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property int $parent_id
 *
 * @property Collection<Recipe> $recipes
 * @property Collection<Category> $children
 * @property Category $parent
 */
class Category extends Model
{
    protected $fillable = [
        'title',
        'parent_id',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(
            Recipe::class,
            'recipe_categories',
            'category_id',
            'recipe_id',
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function scopeParentCategories(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    public function scopeChildrenCategories(Builder $query): void
    {
        $query->where('parent_id');
    }
}
