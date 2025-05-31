<?php

namespace App\Models\Recipe;

use App\Enums\Recipe\Complexity;
use App\Models\Ingredient\Ingredient;
use App\Models\Ingredient\IngredientGroup;
use App\Models\Ingredient\IngredientUnit;
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
 * @property string $url
 * @property array $ingredientTitles
 *
 * @property Source $source
 * @property SourceRecipeUrl $sourceRecipeUrl
 * @property Collection<RecipeIngredient> $recipeIngredients
 * @property Collection<RecipeCategory> $categories
 * @property Collection<IngredientUnit> $ingredientUnits
 * @property Collection<RecipeCuisine> $cuisines
 * @property Collection<RecipeStep> $steps
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
        'is_verified' => 'boolean',
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

    public function ingredientUnits(): BelongsToMany
    {
        return $this->belongsToMany(
            IngredientUnit::class,
            'recipe_ingredients_map',
            'recipe_id',
            'ingredient_unit_id'
        )->withPivot('quantity')
            ->using(RecipeIngredient::class);
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

    public function getIngredientTitlesAttribute(): array
    {
        return $this->ingredientUnits
            ->map(fn(IngredientUnit $unit) => $unit->ingredient->title)
            ->unique()
            ->values()
            ->all();
    }

    public function getUrlAttribute(): ?string
    {
        return $this->sourceRecipeUrl?->url;
    }

    public function getIsVerifiedAttribute(): bool
    {
        return (bool)$this->sourceRecipeUrl->is_verified;
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
