<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $title
 */
class Ingredient extends Model
{
    protected $fillable = [
        'title',
    ];

    public function ingredientUnits(): HasMany
    {
        return $this->hasMany(IngredientUnit::class);
    }
}
