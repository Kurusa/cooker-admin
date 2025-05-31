<?php

namespace App\Models\Recipe;

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
 * @property Collection<RecipeCategory> $children
 * @property Collection<RecipeCategory> $parents
 */
class RecipeCategory extends Model
{
    protected $fillable = [
        'title',
        'parent_id',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(
            Recipe::class,
            'recipe_categories_map',
            'category_id',
            'recipe_id',
        );
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            RecipeCategory::class,
            'recipe_category_parent_map',
            'category_id',
            'parent_id'
        );
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(
            RecipeCategory::class,
            'recipe_category_parent_map',
            'parent_id',
            'category_id'
        );
    }

    public function scopeParentCategories(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    public function scopeChildrenCategories(Builder $query): void
    {
        $query->whereNotNull('parent_id');
    }

    public function getRecipesWithChildrenCountAttribute(): int
    {
        $categoryIds = $this->children()->pluck('id')->push($this->id);

        return Recipe::whereHas('categories', fn($q) => $q->whereIn('id', $categoryIds))->count();
    }
}
