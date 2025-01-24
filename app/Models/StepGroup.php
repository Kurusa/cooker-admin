<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 */
class StepGroup extends Model
{
    protected $fillable = [
        'title',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class, 'step_group_id');
    }
}
