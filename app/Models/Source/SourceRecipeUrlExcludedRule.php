<?php

namespace App\Models\Source;

use App\Enums\Source\SourceRecipeUrlExcludedRuleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $source_id
 * @property SourceRecipeUrlExcludedRuleType $rule_type
 * @property string $value
 *
 * @property Source $source
 */
class SourceRecipeUrlExcludedRule extends Model
{
    protected $fillable = [
        'source_id',
        'rule_type',
        'value',
    ];

    protected $casts = [
        'rule_type' => SourceRecipeUrlExcludedRuleType::class,
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
