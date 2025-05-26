<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\AiProviders\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CookingTimeFormatter;

class VseReceptyParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        $class = 'entry-title';

        return $this->xpathService->extractCleanSingleValue("//h1[@class='$class']");
    }

    public function parseCategories(): array
    {
        return $this->xpathService->extractCleanSingleValue("//ul[@class='recipe-categories']/li[@class='ctg-name'][last()]/a");
    }

    public function parseComplexity(): Complexity
    {
        $difficultyNode = $this->xpath->query("//li[@class='single-meta category-meta'][contains(text(), 'Difficulty:')]");

        $text = $difficultyNode->item(0)?->textContent ?? '';
        $difficulty = strtolower(trim(str_replace('Difficulty:', '', $text)));

        return Complexity::mapParsedValue($difficulty);
    }

    public function parseCookingTime(): ?int
    {
        $timeText = $this->xpath->query("//span[@class='duration']")->item(0)?->textContent;

        return $timeText ? CookingTimeFormatter::formatCookingTime($timeText) : null;
    }

    public function parsePortions(): int
    {
        $rawPortions = $this->xpathService->extractCleanSingleValue("//div[@class='recipe-feature_block recipe-portion']//span[@class='yield']");

        if ($rawPortions) {
            return (int)str_replace(['порції', 'порцій', 'порція'], '', $rawPortions);
        }

        return 1;
    }

    public function parseIngredients(): array
    {
        $ingredients = [];

        $ingredientNodes = $this->xpath->query("//div[@class='recipe-ingredients_list']/ul/li");

        foreach ($ingredientNodes as $ingredientNode) {
            $titleNode = $this->xpath->query("//span[@class='recipe-ingredients_name']", $ingredientNode);
            $amountNode = $this->xpath->query("//span[@class='recipe-ingredients_amount']", $ingredientNode);

            $name = $titleNode->item(0)?->textContent;

            $valueNode = $this->xpath->query("//span[@class='value']", $amountNode->item(0));
            $unitNode = $this->xpath->query("//span[@class='type']", $amountNode->item(0));

            $quantity = $valueNode->item(0)?->textContent ?? '';
            $unit = $unitNode->item(0)?->textContent ?? '';

            $ingredients[] = $name . ': ' . $quantity . ' ' . $unit;
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
    }

    public function parseSteps(): array
    {
        $steps = [];

        $stepNodes = $this->xpath->query("//div[@class='recipe-steps_list instructions']//div[contains(@class, 'recipe-steps_desc')]");

        foreach ($stepNodes as $stepNode) {
            $textNode = $this->xpath->query("//p[contains(@class, 'instruction')]", $stepNode);
            if ($textNode->length > 0) {
                $steps[] = $textNode->item(0)->textContent;
            }
        }

        if (empty($steps)) {
            $stepNodes = $this->xpath->query("//h2[text()='Покроковий рецепт приготування']/following-sibling::ol//li");

            foreach ($stepNodes as $stepNode) {
                $steps[] = $stepNode->textContent;
            }
        }

        return $steps;
    }

    public function parseImage(): string
    {
        $class = 'attachment-post-thumbnail size-post-thumbnail wp-post-image lazyload';

        $imageNode = $this->xpath->query("//img[@class='$class']")->item(0);
        return $imageNode?->getAttribute('data-src') ?? '';
    }

    public function isExcludedByUrlRule(string $url): bool
    {
        return !str_contains($url, '.com/en/');
    }
}
