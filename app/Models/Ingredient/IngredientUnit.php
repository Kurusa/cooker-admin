<?php

namespace App\Models\Ingredient;

use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeIngredient;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $ingredient_id
 * @property int $unit_id
 *
 * @property Ingredient $ingredient
 * @property Unit $unit
 */
class IngredientUnit extends Model
{
    protected $fillable = [
        'ingredient_id',
        'unit_id',
    ];

    public $timestamps = false;

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class, 'ingredient_unit_id');
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(
            Recipe::class,
            'recipe_ingredients_map',
            'ingredient_unit_id',
            'recipe_id'
        )->withPivot('quantity');
    }
}
