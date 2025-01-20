<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\IngredientFormatter;
use DOMNode;
use DOMXPath;

class FayniReceptyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'wprm-recipe-name wprm-block-text-bold';

        return $this->extractCleanSingleValue($xpath, ".//h2[@class='$class']");
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $class = 'trail-items';

        return $this->extractCleanSingleValue($xpath, ".//ul[@class='$class']/li[2]/a/span/text()");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $rawHours = (int) $this->extractCleanSingleValue($xpath, ".//span[contains(@class, 'wprm-recipe-total_time-hours')]/text()");
        $rawMinutes = (int) $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-total_time-minutes')]/text()");

        return ($rawHours * 60) + $rawMinutes;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $class = 'wprm-recipe-ingredients';
        $ingredientNodes = $xpath->query("//ul[@class='$class']/li");

        $parsedIngredients = [];

        /** @var DOMNode $node */
        foreach ($ingredientNodes as $node) {
            $amountNode = $this->extractCleanSingleValue($xpath, ".//span[contains(@class, 'wprm-recipe-ingredient-amount')]", $node);

            $unit = $this->extractCleanSingleValue($xpath, ".//span[contains(@class, 'wprm-recipe-ingredient-unit')]", $node);

            $name = $this->extractCleanSingleValue($xpath, ".//span[contains(@class, 'wprm-recipe-ingredient-name')]", $node);

            $parsedIngredients[] = CleanText::cleanText($name . ': ' . $amountNode . ' ' . $unit);
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($parsedIngredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return array_unique($this->extractMultipleValues($xpath, "//ul[@class='wprm-recipe-instructions']/li/div[@class='wprm-recipe-instruction-text']"));
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//img[@class='attachment-full size-full wp-post-image']")->item(0);
        return $imageNode?->getAttribute('data-wpfc-original-src');
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
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
