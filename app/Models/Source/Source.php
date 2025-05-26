<?php

namespace App\Models\Source;

use App\Enums\Source\SourceStatus;
use App\Models\Recipe\Recipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $url
 * @property string $title
 * @property bool $is_manual
 * @property bool $is_being_parsed
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
        return $this->hasMany(SourceSitemap::class);
    }

    public function recipeUrls(): HasMany
    {
        return $this->hasMany(SourceRecipeUrl::class);
    }

    public function isParsingCompleted(): bool
    {
        return $this->pendingUrlsCount() === 0
            && $this->totalUrls() > 0;
    }

    public function hasUnparsedRecipes(): bool
    {
        return (bool)$this->recipeUrls()
            ->notParsed()
            ->notExcluded()
            ->count();
    }

    public function totalUrls(): int
    {
        return $this->recipeUrls
            ->count();
    }

    public function notExcludedUrlsCount(): int
    {
        return $this->recipeUrls()
            ->notExcluded()
            ->count();
    }

    public function excludedUrlsCount(): int
    {
        return $this->recipeUrls()
            ->isExcluded()
            ->count();
    }

    public function parsedUrlsCount(): int
    {
        return $this->recipes
            ->count();
    }

    public function pendingUrlsCount(): int
    {
        return $this->recipeUrls()
            ->notParsed()
            ->notExcluded()
            ->count();
    }

    public function percentageParsed(): int
    {
        $total = $this->notExcludedUrlsCount();
        $parsed = $this->parsedUrlsCount();

        return ($total > 0 && $parsed <= $total)
            ? (int)round(($parsed / $total) * 100)
            : 100;
    }

    public function getStatus(): SourceStatus
    {
        $total = $this->totalUrls();
        $excluded = $this->excludedUrlsCount();
        $parsed = $this->parsedUrlsCount();
        $pending = $this->pendingUrlsCount();

        if ($total === 0) {
            return SourceStatus::EMPTY;
        }

        if ($parsed === 0 && $excluded === $total) {
            return SourceStatus::EXCLUDED_ONLY;
        }

        if ($parsed === 0 && $pending > 0) {
            return SourceStatus::COLLECTED;
        }

        if ($parsed > 0 && $pending > 0) {
            return SourceStatus::PARTIALLY_PARSED;
        }

        return SourceStatus::PARSED;
    }
}
