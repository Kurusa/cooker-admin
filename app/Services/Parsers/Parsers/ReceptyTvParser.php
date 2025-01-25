<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CookingTimeFormatter;

class ReceptyTvParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractCleanSingleValue("//div[@class='large-8 medium-12 large-offset-2 cell']/h1");
    }

    public function parseCategories(): array
    {
        return $this->xpathService->extractCleanSingleValue("//div[@class='grid-x grid-padding-x']/div[@class='small-12 cell'][1]/ul/li[last()]");
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        $time = $this->xpathService->extractCleanSingleValue("//div[@class='general-info']/div[1]/p[img[@src='https://recepty.24tv.ua/img/clock-icon.svg']]");

        return CookingTimeFormatter::formatCookingTime($time);
    }

    public function parsePortions(): int
    {
        return (int) $this->xpathService->extractCleanSingleValue("//div[@class='general-info']/div[1]/p[img[@src='https://recepty.24tv.ua/img/portion-icon.svg']]");
    }

    public function parseIngredients(bool $debug = false): array
    {
        $ingredients = array_map(fn($item) => str_replace("\n", '', $item->textContent),
            iterator_to_array($this->xpath->query("//div[@class='ingredients']/ul/li[normalize-space(concat(/p or /a, ' ', /span))]")));

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(bool $debug = false): array
    {
        return array_map(fn($item) => [
            'description' => $item->textContent,
            'image' => '',
        ], iterator_to_array($this->xpath->query("//div[@class='recipe-item']/p[position() mod 2 = 0]")));
    }

    public function parseImage(): string
    {
        $src = $this->xpath->query("//div[@class='grid-x grid-padding-x']/div[@class='large-8 medium-12 large-offset-2 cell']/img/@src")->item(0)?->textContent;

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
