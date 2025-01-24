<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $recipe_id
 * @property string $description
 * @property string $image_url
 * @property int $step_group_id
 */
class RecipeStep extends Model
{
    protected $table = 'recipe_steps';

    protected $fillable = [
        'recipe_id',
        'description',
        'image_url',
        'step_group_id',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function stepGroup(): BelongsTo
    {
        return $this->belongsTo(StepGroup::class, 'step_group_id');
    }

    public function hasImage(): bool
    {
        return (bool) $this->image_url;
    }
}
