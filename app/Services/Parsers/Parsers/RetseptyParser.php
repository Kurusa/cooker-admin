<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class RetseptyParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        return $this->xpath->query(
            "//div[contains(@class, 'wprm-recipe wprm-recipe-template-tinysalt-recipe') or contains(@class, 'entry-content')]"
        )->item(0);
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
