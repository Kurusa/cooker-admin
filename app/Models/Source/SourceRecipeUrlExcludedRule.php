<?php

namespace App\Models\Source;

use App\Enums\Source\SourceRecipeUrlExcludedRuleTypeEnum;
use App\Observers\SourceRecipeUrlExcludedRulesObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $source_id
 * @property SourceRecipeUrlExcludedRuleTypeEnum $rule_type
 * @property string $value
 *
 * @property Source $source
 */
#[ObservedBy([SourceRecipeUrlExcludedRulesObserver::class])]
class SourceRecipeUrlExcludedRule extends Model
{
    protected $fillable = [
        'source_id',
        'rule_type',
        'value',
    ];

    protected $casts = [
        'rule_type' => SourceRecipeUrlExcludedRuleTypeEnum::class,
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
