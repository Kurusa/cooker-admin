<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMNode;

class YabpoelaParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractCleanSingleValue("//div[@class='div-form'][1]/h1[@class='title']");
    }

    public function parseCategories(): array
    {
        return $this->xpathService->extractCleanSingleValue("//div[@class='div-form'][1]/a[@class='fullstory-cat']");
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        $cookingTime = $this->xpathService->extractCleanSingleValue("//div[@class='entry-stats__item entry-stats__item_cooking-time']/div[@class='entry-stats__value']");
        $preparationTime = $this->xpathService->extractCleanSingleValue("//div[@class='entry-stats__item entry-stats__item_training-time']/div[@class='entry-stats__value']");

        return CookingTimeFormatter::formatCookingTime($preparationTime) + CookingTimeFormatter::formatCookingTime($cookingTime);
    }

    public function parsePortions(): int
    {
        return (int) $this->xpathService->extractCleanSingleValue("//div[@class='entry-stats__item entry-stats__item_quantity-persons']/div[@class='entry-stats__value']/span");
    }

    public function parseIngredients(bool $debug = false): array
    {
        $ingredients = array_map(fn($item) => $item->textContent,
            iterator_to_array($this->xpath->query("//div[@class='ing_block_list']/ul/li[concat(/span[@class='ingredient_title'], ' ', /span[@class='ing-amount'], ' ', /span[@class='ing-volume'])]")));

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(bool $debug = false): array
    {
        return array_map(function (DOMNode $item) {
            $id = $item->attributes->getNamedItem('id')->textContent;

            $description = $this->xpath->query("//div[@id='$id']/div[@class='fullstory-description']")->item(0)->textContent;
            $image = $this->xpath->query("//div[@id='$id']/div[@class='fullstory-img']/img/@data-src")->item(0)?->textContent;

            return [
                'description' => $description,
                'image'       => $image ? 'https://ua.yabpoela.net' . $image : '',
            ];
        }, iterator_to_array($this->xpath->query("//div[contains(@id,'step-')]")));
    }

    public function parseImage(): string
    {
        $src = $this->xpath->query("//div[@class='div-form'][1]/div[@class='fullstory-img'][1]/img/@src")->item(0)?->textContent;

        return $src ? 'https://ua.yabpoela.net' . $src : '';
    }

    public function urlRule(string $url): bool
    {
        return str_ends_with($url, '.html');
    }
}
