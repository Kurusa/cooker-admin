<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class GoldrecipesParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'post-inner')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'post-ratings')]",
            ".//div[contains(@class, 'page__meta d-flex ai-center')]",
            ".//div[contains(@class, 'heateor_sss_sharing_container heateor_sss_horizontal_sharing')]",
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
