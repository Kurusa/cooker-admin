<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 */
class Ingredient extends Model
{
    protected $fillable = [
        'title',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients', 'ingredient_unit_id', 'recipe_id')
            ->withPivot('quantity')
            ->using(RecipeIngredient::class);
    }

    public function ingredientUnits(): HasMany
    {
        return $this->hasMany(IngredientUnit::class);
    }
}
