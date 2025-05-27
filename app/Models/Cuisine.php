<?php

namespace App\Models;

use App\Models\Recipe\Recipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $title
 */
class Cuisine extends Model
{
    protected $fillable = [
        'title',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_cuisines_map');
    }
}
