<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $title
 */
class Unit extends Model
{
    protected $fillable = [
        'title',
    ];

    public function ingredientUnits(): HasMany
    {
        return $this->hasMany(IngredientUnit::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_units',
            'unit_id',
            'ingredient_id',
        );
    }

    public function recipeIngredients(): HasManyThrough
    {
        return $this->hasManyThrough(
            RecipeIngredient::class,
            IngredientUnit::class,
            'unit_id',
            'ingredient_unit_id',
            'id',
            'id',
        );
    }
}
