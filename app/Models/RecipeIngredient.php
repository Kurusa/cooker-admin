<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RecipeIngredient extends Pivot
{
    protected $table = 'recipe_ingredients';

    protected $fillable = [
        'recipe_id',
        'ingredient_unit_id',
        'quantity',
    ];

    public function ingredientUnit(): BelongsTo
    {
        return $this->belongsTo(IngredientUnit::class, 'ingredient_unit_id');
    }
}
