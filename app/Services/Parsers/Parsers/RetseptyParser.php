<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\BaseRecipeParser;
use DOMXPath;

class RetseptyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, ".//h1[@class='entry-title']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//div[@class='cat-links']/a") ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $span = $xpath->query(".//span[contains(@class, 'wprm-recipe-prep_time-hours')]")->item(0);
        $preparationTimeMinutes = $span ? $span->firstChild->nodeValue * 60 : 0;

        $span = $xpath->query(".//span[@class='wprm-recipe-details wprm-recipe-details-hours wprm-recipe-cook_time wprm-recipe-cook_time-hours']")->item(0);
        $cookingTimeMinutes = $span ? $span->firstChild->nodeValue * 60 : 0;

        return $preparationTimeMinutes + $cookingTimeMinutes;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        return $this->extractCleanSingleValue($xpath, ".//span[@class='wprm-recipe-servings wprm-recipe-details wprm-block-text-normal']") ?? 0;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];

        $ingredientNodes = $xpath->query("//ul[@class='wprm-recipe-ingredients']/li[contains(@class, 'wprm-recipe-ingredient')]");

        foreach ($ingredientNodes as $ingredientNode) {
            $amountNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-amount')]", $ingredientNode);
            $unitNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-unit')]", $ingredientNode);
            $nameNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-name')]", $ingredientNode);

            $ingredients[] = [
                'title' => CleanText::cleanText($nameNode->item(0)?->textContent),
                'unit' => CleanText::cleanText($unitNode->item(0)?->textContent ?? ''),
                'quantity' => CleanText::cleanText($amountNode->item(0)?->textContent ?? ''),
            ];
        }

        return $ingredients;
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];

        $stepNodes = $xpath->query("//ul[@class='wprm-recipe-instructions']/li[contains(@class, 'wprm-recipe-instruction')]");

        foreach ($stepNodes as $stepNode) {
            $textNode = $xpath->query(".//div[contains(@class, 'wprm-recipe-instruction-text')]", $stepNode);
            $text = $textNode->length > 0 ? trim($textNode->item(0)->textContent) : '';

            $steps[] = $text;
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//img[@class='attachment-800x99999 size-800x99999']")->item(0);
        return $imageNode?->getAttribute('data-lazy-src');
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
