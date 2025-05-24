<?php

namespace App\Services\Parsers\Parsers;

use App\DTO\CategoryDTO;
use App\Enums\Recipe\Complexity;
use App\Exceptions\UnsupportedCategoryException;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CookingTimeFormatter;

class PatelnyaParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractCleanSingleValue("//h1[@class='p-name name-title fn']");
    }

    /**
     * @throws UnsupportedCategoryException
     */
    public function parseCategories(): array
    {
        $categoryText = $this->xpathService->extractCleanSingleValue("//div[@class='title-detail']/a/span |
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

        return [
            new CategoryDTO(
                title: $categoryText,
            ),
        ];
    }

    public function parseComplexity(): Complexity
    {
        $complexity = $this->xpathService->extractCleanSingleValue("//div[i/span[contains(text(), 'Рівень складності:')]]/i/span[@class='color-414141']");

        return Complexity::mapParsedValue(mb_strtolower($complexity));
    }

    public function parseCookingTime(): ?int
    {
        $time = $this->xpathService->extractCleanSingleValue("//div[i[@class='duration']]/i/span[@class='color-414141 value-title']");

        return CookingTimeFormatter::formatCookingTime($time);
    }

    public function parsePortions(): int
    {
        $portions = $this->xpathService->extractCleanSingleValue("//div[i/span[contains(text(), 'Кількість порцій:')]]/i/span[@class='color-414141 yield']");

        return $portions ? (int)str_replace(['порцій', 'порція'], '', $portions) : 1;
    }

    public function parseIngredients(bool $debug = false): array
    {
        $ingredients = $this->xpathService->extractMultipleValues("//div[@class='list-ingredient old-list']//ul[@class='ingredient']/li | .//div[@class='list-ingredient old-list']//ul/li");

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients(implode(',', $ingredients));
    }

    public function parseSteps(bool $debug = false): array
    {
        $steps = $this->xpathService->extractMultipleValues("//div[@class='e-instructions step-instructions instructions']//ol/li/text()");

        if (empty($steps)) {
            $steps = $this->xpathService->extractMultipleValues("//div[@class='e-instructions step-instructions instructions']/p/text()[not(contains(., 'Готуємо так:'))]");
        }

        $steps = array_filter(array_map(function ($step) {
            return preg_replace('/[^\PC\s]/u', '', $step);
        }, array_unique($steps)));

        return $debug ? $steps : app(DeepseekService::class)->parseSteps(implode(',', $steps));
    }

    public function parseImage(): string
    {
        return $this->xpathService->extractCleanSingleValue("//img[contains(@class, 'article-img-left')]/@src");
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
