<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;

class FayniReceptyParser extends BaseRecipeParser
{
    public function urlRule(string $url): bool
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

    public function extractRecipeBlock(): string
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'wprm-recipe wprm-recipe-template-cutout')]")->item(0);
        if (!$recipeNode) return '';

        $unwantedXpaths = [
            ".//div[contains(@class, 'wprm-recipe-rating')]",              // рейтинг
            ".//a[contains(@class, 'wprm-recipe-print')]",                 // кнопка друку
            ".//a[contains(@class, 'wprm-recipe-pin')]",                   // кнопка пінтерест
            ".//a[contains(@class, 'wprm-recipe-jump-to-comments')]",     // кнопка коментарів
            ".//div[contains(@class, 'wprm-spacer')]",                    // порожні відступи
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

        $this->removeEmptyDivs($recipeNode);
        $this->removeAllClassesAndAttributes($recipeNode);
        $this->cleanImageAttributes($recipeNode);
        $this->removeGlobalJunkNodes($recipeNode);

        return str_replace("\n", '', $recipeNode->ownerDocument->saveHTML($recipeNode));
    }
}
