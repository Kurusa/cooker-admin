<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $url
 * @property string $title
 * @property string $sitemap_url
 *
 * @property Collection<Recipe> $recipes
 */
class Source extends Model
{
    protected $fillable = [
        'url',
        'title',
        'sitemap_url',
    ];

    public $timestamps = false;

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'source_id');
    }
}
