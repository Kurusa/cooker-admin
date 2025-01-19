<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\BaseRecipeParser;
use DOMXPath;

class NovaStravaParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, ".//h1[@class='sc_layouts_title_caption']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, ".//a[@class='breadcrumbs_item cat_post']") ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $rawHours = $this->extractCleanSingleValue($xpath, ".//span[contains(@class, 'wprm-recipe-total_time-hours')]/text()") ?? 0;
        $rawMinutes = $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-total_time-minutes')]/text()") ?? 0;

        return ($rawHours * 60) + $rawMinutes;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        return $this->extractCleanSingleValue($xpath, ".//span[@class='wprm-recipe-servings wprm-recipe-details wprm-block-text-normal']");
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $rawIngredients = $this->extractMultipleValues($xpath, "//ul[@class='wprm-recipe-ingredients']/li");

        return $this->formatIngredients($rawIngredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return $this->extractMultipleValues($xpath, "//ul[@class='wprm-recipe-instructions']/li");
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//div[@class='wprm-recipe-image wprm-block-image-normal']/img")->item(0);
        return str_replace('150', '370', trim($imageNode?->getAttribute('src')));
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
