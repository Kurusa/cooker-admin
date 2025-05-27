<?php

namespace App\Models\Recipe;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $title
 */
class RecipeCuisine extends Model
{
    protected $fillable = [
        'title',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(
            Recipe::class,
            'recipe_cuisines_map',
            'cuisine_id',
            'recipe_id',
        );
    }
}
