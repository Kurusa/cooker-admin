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
 * @property bool $is_manual
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
        'is_manual',
    ];

    public $timestamps = false;

    protected $casts = [
        'is_manual' => 'boolean',
    ];

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

    public function isParsingCompleted(): bool
    {
        return $this->recipeUrls()->notParsed()->notExcluded()->count() === 0
            && $this->recipeUrls()->count() > 0;
    }

    public function hasUnparsedRecipes(): bool
    {
        return (bool) $this->recipeUrls()->notParsed()->notExcluded()->count();
    }
}
