<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\AiProviders\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class SmachnoParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'in_centr')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'prygot_top')]",
            ".//div[contains(@class, 'soc')]",
            ".//div[contains(@class, 'v_statti')]",
            ".//div[contains(@class, 'prygotuv_zagl')]",
            ".//span[contains(@class, 'comment_id')]",
            ".//div[contains(@class, 'comm_div')]",
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
