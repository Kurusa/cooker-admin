<?php

namespace App\Observers;

use App\Enums\Source\SourceRecipeUrlExcludedRuleType;
use App\Models\Source\SourceRecipeUrl;
use App\Models\Source\SourceRecipeUrlExcludedRule;

class SourceRecipeUrlObserver
{
    public function updating(SourceRecipeUrl $sourceRecipeUrl): void
    {
        if ($sourceRecipeUrl->isDirty('is_excluded') && $sourceRecipeUrl->is_excluded === true) {
            SourceRecipeUrlExcludedRule::create([
                'rule_type' => SourceRecipeUrlExcludedRuleType::EXACT,
                'source_id' => $sourceRecipeUrl->source->id,
                'value' => $sourceRecipeUrl->url,
            ]);
        }
    }
}
