<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class AllRecipesParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'mv-create-wrapper')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'mv-create-nutrition')]",
            ".//div[contains(@class, 'mv-create-products')]",
            ".//div[contains(@class, 'mv-create-notes mv-create-notes-slot-v2')]",
            ".//div[contains(@class, 'mv-create-social')]",
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
