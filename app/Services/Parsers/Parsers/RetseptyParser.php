<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;

class RetseptyParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        $class = 'entry-title';

        return $this->xpathService->extractCleanSingleValue("//h1[@class='$class']");
    }

    public function parseCategories(): array
    {
        $class = 'cat-links';

        return $this->xpathService->extractCleanSingleValue("//div[@class='$class']/a");
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        $span = $this->xpath->query("//span[contains(@class, 'wprm-recipe-prep_time-hours')]")->item(0);
        $preparationTimeMinutes = $span ? $span->firstChild->nodeValue * 60 : 0;

        $span = $this->xpath->query("//span[@class='wprm-recipe-details wprm-recipe-details-hours wprm-recipe-cook_time wprm-recipe-cook_time-hours']")->item(0);
        $cookingTimeMinutes = $span ? $span->firstChild->nodeValue * 60 : 0;

        return $preparationTimeMinutes + $cookingTimeMinutes;
    }

    public function parsePortions(): int
    {
        $portions = (int)$this->xpathService->extractCleanSingleValue("//span[@class='wprm-recipe-servings wprm-recipe-details wprm-block-text-normal']");

        return $portions > 0 ? $portions : 1;
    }

    public function parseIngredients(): array
    {
        $ingredients = [];

        $ingredientNodes = $this->xpath->query("//ul[@class='wprm-recipe-ingredients']/li[contains(@class, 'wprm-recipe-ingredient')]");

        foreach ($ingredientNodes as $ingredientNode) {
            $amountNode = $this->xpath->query("//span[contains(@class, 'wprm-recipe-ingredient-amount')]", $ingredientNode);
            $unitNode = $this->xpath->query("//span[contains(@class, 'wprm-recipe-ingredient-unit')]", $ingredientNode);
            $nameNode = $this->xpath->query("//span[contains(@class, 'wprm-recipe-ingredient-name')]", $ingredientNode);

            $ingredient = $nameNode->item(0)?->textContent . ': ' . $amountNode->item(0)?->textContent ?? ''
            . ' ' . $unitNode->item(0)?->textContent ?? '';
            $ingredients[] = $ingredient;
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
    }

    public function parseSteps(): array
    {
        $steps = [];

        $stepNodes = $this->xpath->query("//ul[@class='wprm-recipe-instructions']/li[contains(@class, 'wprm-recipe-instruction')]");

        foreach ($stepNodes as $stepNode) {
            $textNode = $this->xpath->query("//div[contains(@class, 'wprm-recipe-instruction-text')]", $stepNode);
            $text = $textNode->length > 0 ? trim($textNode->item(0)->textContent) : '';

            $steps[] = $text;
        }

        return $steps;
    }

    public function parseImage(): string
    {
        $imageNode = $this->xpath->query("//img[@class='attachment-800x99999 size-800x99999']")->item(0);
        return $imageNode?->getAttribute('data-lazy-src') ?? '';
    }

    public function isExcludedByUrlRule(string $url): bool
    {
        return true;
    }
}
