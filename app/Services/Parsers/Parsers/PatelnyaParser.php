<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Exceptions\UnsupportedCategoryException;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMXPath;

class PatelnyaParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//h1[@class='p-name name-title fn']");
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $categoryText = $this->extractCleanSingleValue($xpath, "//div[@class='title-detail']/a/span |
                                                           .//div[@id='crumbs']/a/span[last()]");
        $disallowedCategories = [
            'кулінарний словник',
            'новини',
            'дієти',
            'конкурси',
        ];
        foreach ($disallowedCategories as $category) {
            if (str_contains($category, $categoryText)) {
                throw new UnsupportedCategoryException();
            }
        }

        return $category;
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        $complexity = $this->extractCleanSingleValue($xpath, "//div[i/span[contains(text(), 'Рівень складності:')]]/i/span[@class='color-414141']");

        return Complexity::mapParsedValue($complexity);
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $time = $this->extractCleanSingleValue($xpath, "//div[i[@class='duration']]/i/span[@class='color-414141 value-title']");

        return CookingTimeFormatter::formatCookingTime($time);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        $portions = $this->extractCleanSingleValue($xpath, "//div[i/span[contains(text(), 'Кількість порцій:')]]/i/span[@class='color-414141 yield']");

        return $portions ? (int) str_replace(['порцій', 'порція'], '', CleanText::cleanText($portions)) : 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = $this->extractMultipleValues($xpath, "//div[@class='list-ingredient old-list']//ul[@class='ingredient']/li | .//div[@class='list-ingredient old-list']//ul/li");

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = $this->extractMultipleValues($xpath, "//div[@class='e-instructions step-instructions instructions']//ol/li/text()");

        if (empty($steps)) {
            $steps = $this->extractMultipleValues($xpath, "//div[@class='e-instructions step-instructions instructions']/p/text()[not(contains(., 'Готуємо так:'))]");
        }

        return array_filter(array_map(function ($step) {
            return preg_replace('/[^\PC\s]/u', '', CleanText::cleanText($step));
        }, array_unique($steps)));
    }

    public function parseImage(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//img[contains(@class, 'article-img-left')]/@src");
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            'yak-',
            'scho-pr',
            'top-',
            'porad',
            'dijeta-',
            'sumish',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
