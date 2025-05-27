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
 * @property RecipeCategory $parent
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RecipeCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(RecipeCategory::class, 'parent_id');
    }

    public function scopeParentCategories(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    public function scopeChildrenCategories(Builder $query): void
    {
        $query->where('parent_id');
    }

    public function relatedCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return static::query()
            ->whereHas('recipes', function ($query) {
                $query->whereHas('categories', fn($q) => $q->where('id', $this->id));
            })
            ->where('id', '!=', $this->id)
            ->get();
    }


    public function relatedCategoriesCount(): int
    {
        return $this->relatedCategories()->count();
    }
}
