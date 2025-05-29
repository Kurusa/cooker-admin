<?php

namespace App\Observers;

use App\Models\Source\SourceRecipeUrl;
use App\Models\Source\SourceRecipeUrlExcludedRule;

class SourceRecipeUrlObserver
{
    public function deleted(SourceRecipeUrl $sourceRecipeUrl): void
    {
        SourceRecipeUrlExcludedRule::where('value', $sourceRecipeUrl->url)->delete();
    }
}
