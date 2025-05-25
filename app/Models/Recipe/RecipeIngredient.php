<?php

namespace App\Models\Recipe;

use App\Models\Ingredient\IngredientGroup;
use App\Models\Ingredient\IngredientUnit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $recipe_id
 * @property int $ingredient_unit_id
 * @property float $quantity
 * @property int $ingredient_group_id
 *
 * @property IngredientUnit $ingredientUnit
 * @property Recipe $recipe
 */
class RecipeIngredient extends Pivot
{
    protected $table = 'recipe_ingredients';

    protected $fillable = [
        'recipe_id',
        'ingredient_unit_id',
        'quantity',
        'ingredient_group_id',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredientUnit(): BelongsTo
    {
        return $this->belongsTo(IngredientUnit::class, 'ingredient_unit_id');
    }

    public function ingredientGroup(): BelongsTo
    {
        return $this->belongsTo(IngredientGroup::class, 'ingredient_group_id');
    }
}
