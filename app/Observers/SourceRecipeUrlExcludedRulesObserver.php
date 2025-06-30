<?php

namespace App\Observers;

use App\Enums\Source\SourceRecipeUrlExcludedRuleTypeEnum;
use App\Models\Source\SourceRecipeUrlExcludedRule;
use App\Notifications\RecipeUrlExcludedNotification;
use Exception;
use Illuminate\Support\Facades\Notification;

class SourceRecipeUrlExcludedRulesObserver
{
    public function created(SourceRecipeUrlExcludedRule $sourceRecipeUrlExcludedRule): void
    {
        if ($sourceRecipeUrlExcludedRule->rule_type === SourceRecipeUrlExcludedRuleTypeEnum::EXACT) {
            try {
                Notification::route('telegram', config('services.telegram.chat_id'))
                    ->notify(new RecipeUrlExcludedNotification($sourceRecipeUrlExcludedRule->value));
            } catch (Exception $e) {}
        }
    }
}
