<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class ShefkuharParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@id, 'dle-content')]")->item(0);

        $unwantedXpaths = [
            ".//form[contains(@id, 'dle-comments-form')]",
            ".//div[contains(@id, 'dle-ajax-comments')]",
            ".//div[contains(@class, 'kanttaki')]",
            ".//div[contains(@class, 'provse2')]",
            ".//div[contains(@class, 'shemaptstyp')]",
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
