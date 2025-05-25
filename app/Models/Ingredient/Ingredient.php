<?php

namespace App\Models\Ingredient;

use App\Models\Recipe\RecipeIngredient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $title
 */
class Ingredient extends Model
{
    protected $fillable = [
        'title',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(IngredientUnit::class);
    }

    public function recipeIngredients(): HasManyThrough
    {
        return $this->hasManyThrough(
            RecipeIngredient::class,
            IngredientUnit::class,
            'ingredient_id',
            'ingredient_unit_id',
            'id',
            'id',
        );
    }
}
