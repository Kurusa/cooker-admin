<?php

namespace App\Models\Recipe;

use App\Observers\RecipeStepObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $recipe_id
 * @property string $description
 * @property string $image_url
 *
 * @property Recipe $recipe
 */
#[ObservedBy([RecipeStepObserver::class])]
class RecipeStep extends Model
{
    protected $table = 'recipe_steps';

    protected $fillable = [
        'recipe_id',
        'description',
        'image_url',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function hasImage(): bool
    {
        return (bool)$this->image_url;
    }
}
