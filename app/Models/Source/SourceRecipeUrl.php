<?php

namespace App\Models\Source;

use App\Enums\Source\SourceRecipeUrlExcludedRuleTypeEnum;
use App\Models\Recipe\Recipe;
use App\Observers\SourceRecipeUrlObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $source_id
 * @property string $url
 * @property bool|null $is_verified
 * @property-read bool $is_excluded
 *
 * @property-read Source $source
 * @property-read Collection|Recipe[] $recipes
 * @property-read SourceRecipeUrlExcludedRule|null $excludedRule
 *
 * @method static Builder|static isParsed()
 * @method static Builder|static notParsed()
 * @method static Builder|static verified()
 * @method static Builder|static notVerified()
 * @method static Builder|static isExcluded()
 * @method static Builder|static notExcluded()
 *
 * @mixin Builder
 */
#[ObservedBy([SourceRecipeUrlObserver::class])]
class SourceRecipeUrl extends Model
{
    protected $fillable = [
        'source_id',
        'url',
        'is_verified',
    ];

    protected $with = [
        'excludedRule',
    ];

    public $casts = [
        'is_verified' => 'boolean',
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

    public function scopeVerified(Builder $query): void
    {
        $query->where('is_verified', true);
    }

    public function scopeNotVerified(Builder $query): void
    {
        $query->whereNull('is_verified');
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
            'rule_type' => SourceRecipeUrlExcludedRuleTypeEnum::MANUAL_CONTAINS,
            'source_id' => $this->source_id,
            'value' => $this->url,
        ]);
    }
}
