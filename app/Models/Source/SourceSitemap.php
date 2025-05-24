<?php

namespace App\Models\Source;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $source_id
 * @property string url
 *
 * @property Collection<Source> $sources
 */
class SourceSitemap extends Model
{
    protected $fillable = [
        'source_id',
        'url',
    ];

    public $timestamps = false;

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
}
