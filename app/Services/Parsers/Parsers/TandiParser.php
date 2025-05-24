<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;

class TandiParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        $class = 'entry-title';

        return $this->xpathService->extractCleanSingleValue("//h1[@class='$class']");
    }

    public function parseCategories(): array
    {
        $class = 'entry-category';

        return $this->xpathService->extractCleanSingleValue("//li[@class='$class']/a/text()");
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        return null;
    }

    public function parsePortions(): int
    {
        return 1;
    }

    public function parseIngredients(): array
    {
        $ingredientNodes = $this->xpath->query('//p[contains(text(), "Складові:")]/following-sibling::text()');

        $ingredients = [];
        foreach ($ingredientNodes as $ingredientNode) {
            $ingredients[] = $ingredientNode->nodeValue;
        }

        return $ingredients;
        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
    }

    public function parseSteps(): array
    {
        $stepNodes = $this->xpath->query('//strong[contains(text(), "Приготування:")]/following-sibling::text()');

        $steps = [];
        foreach ($stepNodes as $step) {
            $stepText = $step->nodeValue;
            if ($stepText === 'смачного!') {
                continue;
            }

            $stepText = preg_replace('/^\d+\)\. /', '', $stepText);
            $steps[] = $stepText;
        }

        return $steps;
    }

    public function parseImage(): string
    {
        $class = 'entry-thumb td-animation-stack-type0-2';

        return $this->xpath->query("//img[@class='$class']")->item(0)?->getAttribute('src') ?? '';
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
