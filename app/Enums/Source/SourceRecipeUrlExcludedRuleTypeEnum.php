<?php

namespace App\Enums\Source;

enum SourceRecipeUrlExcludedRuleTypeEnum: string
{
    case EXACT = 'exact';
    case MANUAL_CONTAINS = 'manual_contains';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not_contains';
    case REGEX = 'regex';
}
