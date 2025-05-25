<?php

namespace App\Models\Ingredient;

use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeIngredient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $recipe_id
 * @property int $title
 */
class IngredientGroup extends Model
{
    protected $table = 'ingredient_groups';

    protected $fillable = [
        'recipe_id',
        'title',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }
}
