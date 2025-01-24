<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMXPath;

class AllRecipesParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//h1[@class='mv-create-title mv-create-title-primary']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        return $this->extractCleanSingleValue($xpath, "//span[@class='cat-links']/a");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $timeNodes = $xpath->query("//span[@class='mv-time-part mv-time-minutes']");

        $time = $timeNodes->item($timeNodes->length - 1)?->textContent;

        return CookingTimeFormatter::formatCookingTime($time);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $listItems = $xpath->query("//div[@class='mv-create-ingredients']//ul/li");

        $ingredients = [];
        foreach ($listItems as $item) {
            $ingredients[] = CleanText::cleanText($item->textContent);
        }

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $listItems = $xpath->query("//div[@class='mv-create-instructions mv-create-instructions-slot-v2']//ol//p");

        if (!$listItems->length) {
            $listItems = $xpath->query("//div[@class='mv-create-instructions mv-create-instructions-slot-v2']//p");
        }

        $steps = [];
        for ($i = 0; $i < $listItems->length; $i += 2) {
            $title = $listItems->item($i)?->textContent;

            if (!$listItems->item($i + 1)?->getElementsByTagName('img')->length) {
                $title = $listItems->item($i + 1)?->textContent ?? '';
                $steps[] = [
                    'description' => CleanText::cleanText($title),
                    'image' => '',
                ];
                continue;
            }

            $image = $listItems->item($i + 1)->getElementsByTagName('img')->item(0)?->getAttribute('data-src') ?? '';

            $steps[] = [
                'description' => CleanText::cleanText($title),
                'image' => $image
            ];
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query("//figure[@class='post-featured-image']/a/img")->item(0);

        return $imageNode?->getAttribute('data-src') ?? '';
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/ru/',
            '/en/',
            'osoblyvosti',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
