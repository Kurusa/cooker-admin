<?php

namespace App\Models\Recipe;

use App\Enums\Recipe\Complexity;
use App\Models\Ingredient\Ingredient;
use App\Models\Ingredient\IngredientGroup;
use App\Models\Source\Source;
use App\Models\Source\SourceRecipeUrl;
use App\Observers\RecipeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property Complexity $complexity
 * @property int $time
 * @property int $portions
 * @property int $source_recipe_url_id
 * @property string|null $image_url
 * @property bool $is_verified
 *
 * @property Source $source
 * @property SourceRecipeUrl $sourceRecipeUrl
 * @property string $ingredient_list
 * @property Collection<RecipeIngredient> $recipeIngredients
 * @property Collection<RecipeCategory> $categories
 * @property Collection<Ingredient> $ingredients
 * @property Collection<RecipeCuisine> $cuisines
 */
#[ObservedBy([RecipeObserver::class])]
class Recipe extends Model
{
    protected $fillable = [
        'title',
        'complexity',
        'time',
        'portions',
        'image_url',
        'source_recipe_url_id',
        'is_verified',
    ];

    protected $casts = [
        'complexity' => Complexity::class,
        'is_verified' => 'booleans',
    ];

    public function sourceRecipeUrl(): BelongsTo
    {
        return $this->belongsTo(SourceRecipeUrl::class, 'source_recipe_url_id');
    }

    public function source(): HasOneThrough
    {
        return $this->hasOneThrough(
            Source::class,
            SourceRecipeUrl::class,
            'id',
            'id',
            'source_recipe_url_id',
            'source_id',
        );
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(
            Ingredient::class,
            'recipe_ingredients_map',
            'recipe_id',
            'ingredient_unit_id',
        )
            ->withPivot('quantity')
            ->using(RecipeIngredient::class)
            ->with('unit');
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            RecipeCategory::class,
            'recipe_categories_map',
            'recipe_id',
            'category_id'
        );
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function cuisines(): BelongsToMany
    {
        return $this->belongsToMany(
            RecipeCuisine::class,
            'recipe_cuisines_map',
            'recipe_id',
            'cuisine_id',
        );
    }

    public function ingredientGroups(): HasMany
    {
        return $this->hasMany(IngredientGroup::class);
    }

    public function hasImage(): bool
    {
        return !empty($this->image_url);
    }

    public function getUrlAttribute(): string
    {
        return $this->sourceRecipeUrl->url;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderByRaw("
                CASE
                    WHEN complexity = 'easy' THEN 1
                    WHEN complexity = 'medium' THEN 2
                    WHEN complexity = 'hard' THEN 3
                    ELSE 4
                END
            ");
        });
    }
}
