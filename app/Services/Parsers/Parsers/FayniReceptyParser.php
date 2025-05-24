<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class FayniReceptyParser extends BaseRecipeParser
{
    public function isExcludedByUrlRule(string $url): bool
    {
        $disallowedPatterns = [
            'fayni-recepty.com.ua/yak-',
            '/blog',
            'novyi-rik',
            'novyy-rik',
            'kvashena-kapusta-koryst',
            'halva-koryst-ta-shkoda',
            'pisni-stravy-na-pist',
            'shcho-pryhotuvaty',
            'sho-pryhotuvaty',
            'sho-pyhotuvaty',
            'koryst',
            'retsepty',
            'shcho-',
            'stravy-',
            'vse-pro',
            'chym-',
            'den-',
            'vlastyvosti-',
            'korysni-',
            'tradytsiyi-',
            'yak-hotuvaty',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }

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

        $categoriesNode = $this->xpath->query("//p[contains(@class, 'ast-terms-link')]")?->item(0);
        if ($categoriesNode) {
            $clone = $categoriesNode->cloneNode(true);
            $recipeNode->insertBefore($clone, $recipeNode->firstChild);
        }

        return $recipeNode;
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
