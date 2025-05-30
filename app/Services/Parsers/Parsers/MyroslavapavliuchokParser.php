<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class MyroslavapavliuchokParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'page__col-left')]")->item(0);

        $unwantedXpaths = [
            ".//footer",
            ".//div[contains(@class, 'page__date')]",
            ".//div[contains(@class, 'page__meta d-flex ai-center')]",
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
