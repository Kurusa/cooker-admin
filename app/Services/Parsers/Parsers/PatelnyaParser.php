<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMXPath;

class PatelnyaParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'p-name name-title fn';

        return $this->extractCleanSingleValue($xpath, ".//h1[@class='$class']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $class = 'title-detail';

        return $this->extractCleanSingleValue($xpath, ".//div[@class='$class']/a/span") ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        $rawComplexity = $this->extractCleanSingleValue($xpath, ".//div[i/span[contains(text(), 'Рівень складності:')]]/i/span[@class='color-414141']");
        return Complexity::mapParsedValue($rawComplexity);
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $rawTime = $this->extractCleanSingleValue($xpath, ".//div[i[@class='duration']]/i/span[@class='color-414141 value-title']");
        return CookingTimeFormatter::formatCookingTime($rawTime);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        $rawPortions = $this->extractCleanSingleValue($xpath, ".//div[i/span[contains(text(), 'Кількість порцій:')]]/i/span[@class='color-414141 yield']");

        if ($rawPortions) {
            return (int) str_replace(['порцій', 'порція'], '', CleanText::cleanText($rawPortions));
        }

        return 1;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $rawIngredients = $this->extractMultipleValues($xpath, ".//div[@class='list-ingredient old-list']//ul[@class='ingredient']/li");
        if (!count($rawIngredients)) {
            $rawIngredients = $this->extractMultipleValues($xpath, ".//div[@class='list-ingredient old-list']//ul/li");
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($rawIngredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];

        $listItems = $xpath->query(".//div[@class='e-instructions step-instructions instructions']//ol/li");
        foreach ($listItems as $item) {
            $steps[] = $item->textContent;
        }

        if (count($steps)) {
            return array_unique($steps);
        }

        $brs = $xpath->query(".//div[@class='e-instructions step-instructions instructions']/p");
        foreach ($brs as $node) {
            $text = $node->textContent;
            if ($text == 'Готуємо так:') {
                continue;
            }

            $text = substr(str_replace('<br>', "\n", $text), 3);
            $exploded = explode("\n", $text);
            if (!count($exploded)) {
                $exploded = explode("<br>", $text);
            }

            $steps = array_merge($steps, array_filter($exploded));
        }

        return array_filter(array_map(function ($step) {
            return preg_replace('/[^\PC\s]/u', '', $step);
        }, $steps));
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query(".//img[contains(@class, 'article-img-left')]")->item(0);

        if ($src = $imageNode->getAttribute('src')) {
            return $src;
        }

        return '';
    }

    public function urlRule(string $url): bool
    {
        return !str_contains($url, 'yak-');
    }
}
