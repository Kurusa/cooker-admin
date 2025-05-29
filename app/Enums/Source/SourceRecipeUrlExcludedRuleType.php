<?php

namespace App\Enums\Source;

enum SourceRecipeUrlExcludedRuleType: string
{
    case EXACT = 'exact';
    case CONTAINS = 'contains';
    case REGEX = 'regex';
}
