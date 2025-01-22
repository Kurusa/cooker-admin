<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMXPath;

class ReceptyTvParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//div[@class='large-8 medium-12 large-offset-2 cell']/h1");
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//div[@class='grid-x grid-padding-x']/div[@class='small-12 cell'][1]/ul/li[last()]");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $time = $this->extractCleanSingleValue($xpath, "//div[@class='general-info']/div[1]/p[img[@src='https://recepty.24tv.ua/img/clock-icon.svg']]");

        return CookingTimeFormatter::formatCookingTime($time);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return (int) $this->extractCleanSingleValue($xpath, "//div[@class='general-info']/div[1]/p[img[@src='https://recepty.24tv.ua/img/portion-icon.svg']]");
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = array_map(fn($item) => CleanText::cleanText(str_replace("\n", '', $item->textContent)),
            iterator_to_array($xpath->query("//div[@class='ingredients']/ul/li[normalize-space(concat(/p or /a, ' ', /span))]")));

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return array_map(fn($item) => [
            'description' => CleanText::cleanText($item->textContent),
            'image' => '',
        ], iterator_to_array($xpath->query("//div[@class='recipe-item']/p[position() mod 2 = 0]")));
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $src = $xpath->query("//div[@class='grid-x grid-padding-x']/div[@class='large-8 medium-12 large-offset-2 cell']/img/@src")->item(0)?->textContent;

        return $src ? 'https://recepty.24tv.ua' . $src : '';
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/ru/'
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
