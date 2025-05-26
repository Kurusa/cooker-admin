<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class FoodcourtParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        return $this->xpath->query("//div[contains(@class, 'post-content-content')]")?->item(0);
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }

    public function getSourceKey(): string
    {
        return 'foodcourt';
    }
}
