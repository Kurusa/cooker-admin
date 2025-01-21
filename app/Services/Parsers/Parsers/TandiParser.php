<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMXPath;

class TandiParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'entry-title';

        return $this->extractCleanSingleValue($xpath, "//h1[@class='$class']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $class = 'entry-category';

        return $this->extractCleanSingleValue($xpath, ".//li[@class='$class']/a/text()");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredientNodes = $xpath->query('//p[contains(text(), "Складові:")]/following-sibling::text()');

        $ingredients = [];
        foreach ($ingredientNodes as $ingredientNode) {
            $ingredients[] = CleanText::cleanText($ingredientNode->nodeValue);
        }

        return $ingredients;
        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $stepNodes = $xpath->query('//strong[contains(text(), "Приготування:")]/following-sibling::text()');

        $steps = [];
        foreach ($stepNodes as $step) {
            $stepText = CleanText::cleanText($step->nodeValue);
            if ($stepText === 'смачного!') {
                continue;
            }

            $stepText = preg_replace('/^\d+\)\. /', '', $stepText);
            $steps[] = $stepText;
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $class = 'entry-thumb td-animation-stack-type0-2';

        return $xpath->query(".//img[@class='$class']")->item(0)?->getAttribute('src') ?? '';
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
