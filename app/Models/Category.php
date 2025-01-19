<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 *
 * @property Collection<Recipe> $recipes
 */
class Category extends Model
{
    protected $fillable = [
        'title',
    ];

    public $timestamps = false;

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
