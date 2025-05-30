<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class SmakotaInfoParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'inside-article')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'entry-meta')]",
            ".//div[contains(@class, 'crp_related crp-masonry')]",
            ".//footer",
        ];

        foreach ($unwantedXpaths as $xpath) {
            $nodes = $this->xpath->query($xpath, $recipeNode);

            foreach (iterator_to_array($nodes) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        return $recipeNode;
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
