<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMXPath;

class RetseptyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'entry-title';

        return $this->extractCleanSingleValue($xpath, ".//h1[@class='$class']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $class = 'cat-links';

        return $this->extractCleanSingleValue($xpath, "//div[@class='$class']/a") ?? '';
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

    public function parsePortions(DOMXPath $xpath): int
    {
        $portions = (int) $this->extractCleanSingleValue($xpath, ".//span[@class='wprm-recipe-servings wprm-recipe-details wprm-block-text-normal']");

        return $portions > 0 ? $portions : 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = [];

        $ingredientNodes = $xpath->query("//ul[@class='wprm-recipe-ingredients']/li[contains(@class, 'wprm-recipe-ingredient')]");

        foreach ($ingredientNodes as $ingredientNode) {
            $amountNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-amount')]", $ingredientNode);
            $unitNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-unit')]", $ingredientNode);
            $nameNode = $xpath->query(".//span[contains(@class, 'wprm-recipe-ingredient-name')]", $ingredientNode);

            $ingredient = CleanText::cleanText($nameNode->item(0)?->textContent) . ': ' . CleanText::cleanText($amountNode->item(0)?->textContent ?? '')
                . ' ' . CleanText::cleanText($unitNode->item(0)?->textContent ?? '');
            $ingredients[] = $ingredient;
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
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

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query(".//img[@class='attachment-800x99999 size-800x99999']")->item(0);
        return $imageNode?->getAttribute('data-lazy-src') ?? '';
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
