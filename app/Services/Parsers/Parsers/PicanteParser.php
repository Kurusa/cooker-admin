<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class PicanteParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//article[contains(@class, 'recipeTranslatable-detail hrecipe')]")->item(0);

        $unwantedXpaths = [
            ".//section[contains(@class, 'recipes-list related-recipes-list')]",
            ".//div[contains(@class, 'row dateprint')]",
            ".//ul[contains(@class, 'taglist')]",
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

    public function getSourceKey(): string
    {
        return 'picante';
    }
}
