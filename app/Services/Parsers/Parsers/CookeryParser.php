<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class CookeryParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@id, 'newsfullstory')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'related-block')]",
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
