<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMNode;
use DOMXPath;

class YabpoelaParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//div[@class='div-form'][1]/h1[@class='title']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        return $this->extractCleanSingleValue($xpath, "//div[@class='div-form'][1]/a[@class='fullstory-cat']");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $cookingTime = $this->extractCleanSingleValue($xpath, "//div[@class='entry-stats__item entry-stats__item_cooking-time']/div[@class='entry-stats__value']");
        $preparationTime = $this->extractCleanSingleValue($xpath, "//div[@class='entry-stats__item entry-stats__item_training-time']/div[@class='entry-stats__value']");

        return CookingTimeFormatter::formatCookingTime($preparationTime) + CookingTimeFormatter::formatCookingTime($cookingTime);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return (int) $this->extractCleanSingleValue($xpath, "//div[@class='entry-stats__item entry-stats__item_quantity-persons']/div[@class='entry-stats__value']/span");
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = array_map(fn($item) => CleanText::cleanText($item->textContent),
            iterator_to_array($xpath->query("//div[@class='ing_block_list']/ul/li[concat(/span[@class='ingredient_title'], ' ', /span[@class='ing-amount'], ' ', /span[@class='ing-volume'])]")));

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return array_map(function (DOMNode $item) use ($xpath) {
            $id = $item->attributes->getNamedItem('id')->textContent;

            $description = $xpath->query("//div[@id='$id']/div[@class='fullstory-description']")->item(0)->textContent;
            $image = $xpath->query("//div[@id='$id']/div[@class='fullstory-img']/img/@data-src")->item(0)?->textContent;

            return [
                'description' => $description,
                'image' => $image ? 'https://ua.yabpoela.net' . $image : '',
            ];
        }, iterator_to_array($xpath->query("//div[contains(@id,'step-')]")));
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $src = $xpath->query("//div[@class='div-form'][1]/div[@class='fullstory-img'][1]/img/@src")->item(0)?->textContent;

        return $src ? 'https://ua.yabpoela.net' . $src : '';
    }

    public function urlRule(string $url): bool
    {
        return str_ends_with($url, '.html');
    }
}
