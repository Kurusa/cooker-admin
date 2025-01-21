<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $source_id
 * @property string $url
 * @property bool $is_parsed
 * @property bool $is_excluded
 *
 * @property Collection<Source> $sources
 */
class SourceRecipeUrl extends Model
{
    protected $fillable = [
        'source_id',
        'url',
        'is_parsed',
        'is_excluded',
    ];

    public $casts = [
        'is_parsed' => 'boolean',
        'is_excluded' => 'boolean',
    ];

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class, 'source_id');
    }
}
