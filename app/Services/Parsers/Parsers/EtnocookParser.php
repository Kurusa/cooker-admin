<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMNode;
use DOMXPath;

class EtnocookParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//h1[@class='entry-title']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        return $this->extractCleanSingleValue($xpath, "//a[rel='category tag'][1]");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $cookingTime = $this->extractCleanSingleValue($xpath, "//p[contains(., 'Час приготування')]");

        return CookingTimeFormatter::formatCookingTime($cookingTime);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = [];

        array_map(function (DOMNode $item) use (&$ingredients) {
            $exploded = explode("<br>", $item->textContent);

            foreach ($exploded as $ingredient) {
                $ingredients[] = CleanText::cleanText($ingredient);
            }
        }, iterator_to_array($xpath->query("//p[contains(.,'………')]")));

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];

        return array_map(function (DOMNode $item) use (&$steps) {
            $exploded = explode("<br>", $item->textContent);

            foreach ($exploded as $step) {
                $steps[] = [
                    'description' => CleanText::cleanText($step),
                    'image' => '',
                ];
            }
        }, iterator_to_array($xpath->query("//p[contains(.,'………')]")));
    }

    public function parseImage(DOMXPath $xpath): string
    {
        return $xpath->query("//img[contains(@class, 'aligncenter size-full wp-image-')]/@src")->item(0)?->textContent;
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/tag/',
            'porada-dnya',
            'yak-',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
