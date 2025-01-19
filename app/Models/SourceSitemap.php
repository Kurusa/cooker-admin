<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $source_id
 * @property string $sitemap_url
 *
 * @property Collection<Source> $sources
 */
class SourceSitemap extends Model
{
    protected $fillable = [
        'source_id',
        'sitemap_url',
    ];

    public $timestamps = false;

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class, 'source_id');
    }
}
