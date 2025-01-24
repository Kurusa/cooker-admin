<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;

class FayniReceptyParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return CleanText::cleanText($this->xpath->query("//h2[@class='wprm-recipe-name wprm-block-text-bold']")?->item(0)?->nodeValue ?? '');
    }

    public function parseCategories(): array
    {
        return CleanText::cleanText($this->xpath->query("//ul[@class='trail-items']/li[2]/a/span/text()")?->item(0)?->nodeValue ?? '');
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        $rawHours = (int) $this->xpathService->extractCleanSingleValue("//span[contains(@class, 'wprm-recipe-total_time-hours')]/text()");
        $rawMinutes = (int) $this->xpathService->extractCleanSingleValue("//span[contains(@class, 'wprm-recipe-total_time-minutes')]/text()");

        return ($rawHours * 60) + $rawMinutes;
    }

    public function parsePortions(): int
    {
        return 1;
    }

    public function parseIngredients(bool $debug = false): array
    {
        $ingredients = [];
        $nodes = $this->xpath->query("//ul[@class='wprm-recipe-ingredients']/li");

        foreach ($nodes as $node) {
            $ingredient = implode(' ', [
                $this->xpathService->extractCleanSingleValue("//span[contains(@class, 'wprm-recipe-ingredient-name')]", $node),
                $this->xpathService->extractCleanSingleValue("//span[contains(@class, 'wprm-recipe-ingredient-amount')]", $node),
                $this->xpathService->extractCleanSingleValue("//span[contains(@class, 'wprm-recipe-ingredient-unit')]", $node),
            ]);

            $ingredients[] = CleanText::cleanText($ingredient);
        }

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(bool $debug = false): array
    {
        return array_unique($this->xpathService->extractMultipleValues("//ul[@class='wprm-recipe-instructions']/li/div[@class='wprm-recipe-instruction-text']"));
    }

    public function parseImage(): string
    {
        return $this->xpathService->extractCleanSingleValue("//img[@class='attachment-full size-full wp-post-image']/@data-wpfc-original-src");
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
