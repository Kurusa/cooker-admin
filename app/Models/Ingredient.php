<?php

namespace App\Models;

use App\Models\Recipe\RecipeIngredient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $title
 * @property string $original_title
 */
class Ingredient extends Model
{
    protected $fillable = [
        'title',
        'original_title',
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
