<?php

namespace App\Models\Source;

use App\Enums\Source\SourceStatusEnum;
use App\Models\Recipe\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

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
 * @property Collection<SourceRecipeUrlExcludedRule> $excludedRules
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

    public function excludedRules(): HasMany
    {
        return $this->hasMany(SourceRecipeUrlExcludedRule::class);
    }

    public function unparsedRecipes(): HasMany
    {
        return $this->recipeUrls()
            ->notParsed()
            ->notExcluded();
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
        return $this->recipeUrls->filter(fn(SourceRecipeUrl $url) => $url->excludedRule !== null)->count();
    }

    public function parsedUrlsCount(): int
    {
        return $this->recipes()
            ->select('recipes.source_recipe_url_id')
            ->distinct()
            ->count('recipes.source_recipe_url_id');
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

    public function getParsedSummaryText(): string
    {
        $parsed = $this->parsedUrlsCount();
        $total = $this->notExcludedUrlsCount();
        $percent = $this->percentageParsed();

        return "{$parsed} recipes (out of {$total}) — {$percent}%";
    }

    public function getStatus(): SourceStatusEnum
    {
        $total = $this->totalUrls();
        $excluded = $this->excludedUrlsCount();
        $parsed = $this->parsedUrlsCount();
        $pending = $this->pendingUrlsCount();

        if ($total === 0) {
            return SourceStatusEnum::EMPTY;
        }

        if ($parsed === 0 && $excluded === $total) {
            return SourceStatusEnum::EXCLUDED_ONLY;
        }

        if ($parsed === 0 && $pending > 0) {
            return SourceStatusEnum::COLLECTED;
        }

        if ($parsed > 0 && $pending > 0) {
            return SourceStatusEnum::PARTIALLY_PARSED;
        }

        return SourceStatusEnum::PARSED;
    }
}
