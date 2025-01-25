<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $ingredient_id
 * @property int $unit_id
 */
class IngredientUnit extends Model
{
    protected $fillable = [
        'ingredient_id',
        'unit_id',
    ];

    public $timestamps = false;

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
