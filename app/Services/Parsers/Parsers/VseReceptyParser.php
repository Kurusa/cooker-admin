<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\BaseRecipeParser;
use DOMXPath;

class VseReceptyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'entry-title';

        return $this->extractCleanSingleValue($xpath, ".//h1[@class='$class']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, '//ul[@class="recipe-categories"]/li[@class="ctg-name"][last()]/a') ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        $difficultyNode = $xpath->query("//li[@class='single-meta category-meta'][contains(text(), 'Difficulty:')]");

        $text = $difficultyNode->item(0)?->textContent ?? '';
        $difficulty = strtolower(trim(str_replace('Difficulty:', '', $text)));

        return Complexity::mapParsedValue($difficulty);
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $timeText = $xpath->query("//span[@class='duration']")->item(0)?->textContent;

        $totalMinutes = 0;

        if (preg_match('/(\d+)\s*(година|години|год)/iu', $timeText, $matches)) {
            $totalMinutes += (int) $matches[1] * 60;
        }

        if (preg_match('/(\d+)\s*(хвилина|хвилини|хв)/iu', $timeText, $matches)) {
            $totalMinutes += (int) $matches[1];
        }

        return $totalMinutes;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        $rawPortions = $this->extractCleanSingleValue($xpath, ".//div[@class='recipe-feature_block recipe-portion']//span[@class='yield']");

        if ($rawPortions) {
            return (int) str_replace(['порції', 'порцій', 'порція'], '', CleanText::cleanText($rawPortions));
        }

        return null;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];

        $ingredientNodes = $xpath->query("//div[@class='recipe-ingredients_list']/ul/li");

        foreach ($ingredientNodes as $ingredientNode) {
            $titleNode = $xpath->query(".//span[@class='recipe-ingredients_name']", $ingredientNode);
            $amountNode = $xpath->query(".//span[@class='recipe-ingredients_amount']", $ingredientNode);

            $name = CleanText::cleanText($titleNode->item(0)?->textContent);

            $valueNode = $xpath->query(".//span[@class='value']", $amountNode->item(0));
            $unitNode = $xpath->query(".//span[@class='type']", $amountNode->item(0));

            $quantity = CleanText::cleanText($valueNode->item(0)?->textContent ?? '');
            $unit = CleanText::cleanText($unitNode->item(0)?->textContent ?? '');

            $ingredients[] = [
                'title' => $name,
                'quantity' => $quantity,
                'unit' => $unit,
            ];
        }

        return $ingredients;
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];

        $stepNodes = $xpath->query("//div[@class='recipe-steps_list instructions']//div[contains(@class, 'recipe-steps_desc')]");

        foreach ($stepNodes as $stepNode) {
            $textNode = $xpath->query(".//p[contains(@class, 'instruction')]", $stepNode);
            if ($textNode->length > 0) {
                $steps[] = CleanText::cleanText($textNode->item(0)->textContent);
            }
        }

        if (empty($steps)) {
            $stepNodes = $xpath->query("//h2[text()='Покроковий рецепт приготування']/following-sibling::ol//li");

            foreach ($stepNodes as $stepNode) {
                $steps[] = CleanText::cleanText($stepNode->textContent);
            }
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $class = 'attachment-post-thumbnail size-post-thumbnail wp-post-image lazyload';

        $imageNode = $xpath->query(".//img[@class='$class']")->item(0);
        return $imageNode?->getAttribute('data-src');
    }

    public function urlRule(string $url): bool
    {
        return !str_contains($url, '.com/en/');
    }
}
