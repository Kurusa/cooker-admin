<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\BaseRecipeParser;
use DOMXPath;

class FayniReceptyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = "wprm-recipe-name wprm-block-text-bold";

        return $this->extractSingleValue($xpath, ".//h2[@class='$class']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $class = "trail-items";

        return $this->extractSingleValue($xpath, ".//ul[@class='$class']/li[2]/a/span/text()") ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $rawHours = $this->extractSingleValue($xpath, ".//span[contains(@class, 'wprm-recipe-total_time-hours')]/text()") ?? 0;
        $rawMinutes = $this->extractSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-total_time-minutes')]/text()") ?? 0;

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
        foreach ($ingredientNodes as $node) {
            $amountNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-amount')]", $node);
            $amount = $amountNode->length > 0 ? str_replace(',', '.', $this->cleanText($amountNode[0]->textContent)) : null;

            $unitNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-unit')]", $node);
            $unit = $unitNode->length > 0 ? $this->translateUnit($this->cleanText($unitNode[0]->textContent)) : null;

            $nameNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-name')]", $node);
            $name = $nameNode->length > 0 ? $this->cleanText($nameNode[0]->textContent) : null;

            $parsedIngredients[] = [
                'title' => $name,
                'quantity' => $amount,
                'unit' => $unit,
            ];
        }

        return $parsedIngredients;
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return $this->extractMultipleValues($xpath, "//ul[@class='wprm-recipe-instructions']/li/div[@class='wprm-recipe-instruction-text']");
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//img[@class='attachment-full size-full wp-post-image']")->item(0);
        return $imageNode?->getAttribute('data-wpfc-original-src');
    }

    protected function translateUnit(?string $unit): ?string
    {
        $translations = [
            'kg' => 'кг',
            'g' => 'г',
            'cups' => 'склянки',
            'tbsp' => 'ст. л',
            'tsp' => 'ч. л',
            'ml' => 'мл',
            'l' => 'л',
            'qt' => 'шт',
            'mg' => 'мг',
        ];

        return $translations[$unit] ?? $unit;
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
