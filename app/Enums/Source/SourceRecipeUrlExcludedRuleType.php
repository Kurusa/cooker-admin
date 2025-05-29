<?php

namespace App\Enums\Source;

enum SourceRecipeUrlExcludedRuleType: string
{
    case EXACT = 'exact';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not_contains';
    case REGEX = 'regex';
}
