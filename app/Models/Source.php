<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $url
 * @property string $title
 *
 * @property Collection<Recipe> $recipes
 * @property Collection<SourceSitemap> $sitemapUrls
 */
class Source extends Model
{
    protected $fillable = [
        'url',
        'title',
    ];

    public $timestamps = false;

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'source_id');
    }

    public function sitemapUrls(): HasMany
    {
        return $this->hasMany(SourceSitemap::class);
    }
}
