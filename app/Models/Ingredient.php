<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function units(): HasMany
    {
        return $this->hasMany(IngredientUnit::class);
    }
}
