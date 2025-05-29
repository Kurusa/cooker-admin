<?php

namespace App\Observers;

use App\Enums\Source\SourceRecipeUrlExcludedRuleType;
use App\Models\Source\SourceRecipeUrlExcludedRule;
use App\Notifications\RecipeUrlExcludedNotification;
use Illuminate\Support\Facades\Notification;

class SourceRecipeUrlExcludedRulesObserver
{
    public function created(SourceRecipeUrlExcludedRule $sourceRecipeUrlExcludedRule): void
    {
        if ($sourceRecipeUrlExcludedRule->rule_type === SourceRecipeUrlExcludedRuleType::EXACT) {
            Notification::route('telegram', config('services.telegram.chat_id'))
                ->notify(new RecipeUrlExcludedNotification($sourceRecipeUrlExcludedRule->value));
        }
    }
}
