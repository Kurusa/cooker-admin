<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMXPath;

class FayniReceptyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return CleanText::cleanText($xpath->query("//h2[@class='wprm-recipe-name wprm-block-text-bold']")?->item(0)?->nodeValue ?? '');
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return CleanText::cleanText($xpath->query("//ul[@class='trail-items']/li[2]/a/span/text()")?->item(0)?->nodeValue ?? '');
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $rawHours = (int) $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-total_time-hours')]/text()");
        $rawMinutes = (int) $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-total_time-minutes')]/text()");

        return ($rawHours * 60) + $rawMinutes;
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = [];
        $nodes = $xpath->query("//ul[@class='wprm-recipe-ingredients']/li");

        foreach ($nodes as $node) {
            $ingredient = implode(' ', [
                $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-ingredient-name')]", $node),
                $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-ingredient-amount')]", $node),
                $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-ingredient-unit')]", $node),
            ]);

            $ingredients[] = CleanText::cleanText($ingredient);
        }

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return array_unique($this->extractMultipleValues($xpath, "//ul[@class='wprm-recipe-instructions']/li/div[@class='wprm-recipe-instruction-text']"));
    }

    public function parseImage(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//img[@class='attachment-full size-full wp-post-image']/@data-wpfc-original-src");
    }

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
}
