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

    public function isExcludedByUrlRule(string $url): bool
    {
        $disallowedPatterns = [
            'yak-',
            'scho-pr',
            'top-',
            'porad',
            'dijeta-',
            'sumish',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
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
            'здорове харчування',
            'кулінарні таблиці',
        ];

        $categoryValue = $xpath->query("//div[@class='title-detail']/a/span | .//div[@id='crumbs']/a/span[last()]")->item(0)->nodeValue;

        foreach ($excludedCategories as $excludedCategory) {
            if (str_contains(mb_strtolower($categoryValue), $excludedCategory)) {
                return true;
            }
        }

        return false;
    }

    public function getSourceKey(): string
    {
        return 'patelnya';
    }
}
