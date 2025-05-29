<?php

namespace App\Models\Source;

use App\Enums\Source\SourceRecipeUrlExcludedRuleType;
use App\Models\Recipe\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $source_id
 * @property string $url
 * @property bool $is_excluded
 *
 * @property Source $source
 * @property SourceRecipeUrlExcludedRule $excludedRule
 */
class SourceRecipeUrl extends Model
{
    protected $fillable = [
        'source_id',
        'url',
    ];

    protected $with = [
        'excludedRule',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'source_recipe_url_id');
    }

    public function excludedRule(): HasOne
    {
        return $this->hasOne(SourceRecipeUrlExcludedRule::class, 'value', 'url');
    }

    public function getIsExcludedAttribute(): bool
    {
        return $this->excludedRule()->exists();
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
        $query->whereHas('excludedRule');
    }

    public function scopeNotExcluded(Builder $query): void
    {
        $query->whereDoesntHave('excludedRule');
    }

    public function exclude()
    {
        return $this->excludedRule()->create([
            'rule_type' => SourceRecipeUrlExcludedRuleType::EXACT,
            'source_id' => $this->source_id,
            'value' => $this->url,
        ]);
    }
}
