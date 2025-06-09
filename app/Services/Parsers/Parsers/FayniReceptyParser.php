<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class FayniReceptyParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'wprm-recipe wprm-recipe-template-cutout')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'wprm-recipe-rating')]",
            ".//a[contains(@class, 'wprm-recipe-print')]",
            ".//a[contains(@class, 'wprm-recipe-pin')]",
            ".//a[contains(@class, 'wprm-recipe-jump-to-comments')]",
            ".//div[contains(@class, 'wprm-spacer')]",
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
