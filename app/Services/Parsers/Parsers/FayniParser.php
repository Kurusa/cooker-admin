<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMXPath;

class FayniParser extends BaseRecipeParser
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
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $recipeNode = $xpath->query("//div[contains(@class, 'wprm-recipe wprm-recipe-template-cutout')]")?->item(0);

        if (!$recipeNode) {
            return true;
        }

        return false;
    }
}
