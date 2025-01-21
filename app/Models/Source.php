<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $url
 * @property string $title
 *
 * @property Collection<Recipe> $recipes
 * @property Collection<SourceSitemap> $sitemaps
 * @property Collection<SourceRecipeUrl> $recipeUrls
 */
class Source extends Model
{
    protected $fillable = [
        'url',
        'title',
    ];

    public $timestamps = false;

    public function recipes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Recipe::class,
            SourceRecipeUrl::class,
            'source_id',
            'source_recipe_url_id',
            'id',
            'id',
        );
    }

    public function sitemaps(): HasMany
    {
        return $this->hasMany(SourceSitemap::class, 'source_id');
    }

    public function recipeUrls(): HasMany
    {
        return $this->hasMany(SourceRecipeUrl::class);
    }
}
