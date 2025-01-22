<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMXPath;

class MonchefParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//h1[@class='entry-title']");
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return '';
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
        $listItems = $xpath->query("//div[@class='entry-content']/ul[1]/li");

        $ingredients = [];
        foreach ($listItems as $item) {
            $ingredients[] = CleanText::cleanText($item->textContent);
        }

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $listItems = $xpath->query("//div[@class='entry-content']//following::ul/following-sibling::p");

        $steps = [];
        foreach ($listItems as $item) {
            $steps[] = [
                'description' => CleanText::cleanText($item->textContent),
                'image' => '',
            ];
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query("//div[@class='entry-media-thumb']//@style")->item(0)->nodeValue;
        $imageUrl = str_replace('background-image: url(', '', $imageNode);

        return str_replace(');', '', $imageUrl);
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
