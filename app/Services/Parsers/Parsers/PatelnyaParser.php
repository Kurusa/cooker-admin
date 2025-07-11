<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMXPath;

class PatelnyaParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//article[contains(@class, 'hrecipe')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'leave-comment-wrap margin-top-30')]",
            ".//div[contains(@class, 'data-middle fl color-656464 margin-top-15')]",
            ".//div[contains(@class, 'tags margin-top-20')]",
            ".//div[contains(@class, 'title-ingredient-red font-size-18')]",
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

        $excludedCategories = [
            'кулінарний словник',
            'новини',
            'дієти',
            'конкурси',
            'кулінарні таблиці',
        ];

        $categoryValue = $xpath->query("//div[@class='title-detail']/a/span | .//div[@id='crumbs']/a/span[last()]")->item(0)->nodeValue;

        if (in_array(mb_strtolower($categoryValue), $excludedCategories, true)) {
            return true;
        }

        return false;
    }
}
