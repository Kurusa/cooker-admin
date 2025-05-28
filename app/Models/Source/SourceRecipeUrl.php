<?php

namespace App\Models\Source;

use App\Models\Recipe\Recipe;
use App\Observers\SourceRecipeUrlObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $source_id
 * @property string $url
 * @property bool $is_excluded
 *
 * @property Source $source
 */
#[ObservedBy([SourceRecipeUrlObserver::class])]
class SourceRecipeUrl extends Model
{
    protected $fillable = [
        'source_id',
        'url',
        'is_excluded',
    ];

    public $casts = [
        'is_excluded' => 'boolean',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'source_recipe_url_id');
    }

    public function scopeIsParsed(Builder $query): void
    {
        $query->whereHas('recipes');
    }

    public function scopeNotParsed(Builder $query): void
    {
        $query->whereDoesntHave('recipes');
    }

    public function scopeIsExcluded(Builder $query): void
    {
        $query->where('is_excluded', 1);
    }

    public function scopeNotExcluded(Builder $query): void
    {
        $query->where('is_excluded', 0);
    }
}
