<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

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

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
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
