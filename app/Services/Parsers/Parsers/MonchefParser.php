<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;

class MonchefParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractCleanSingleValue("//h1[@class='entry-title']");
    }

    public function parseCategories(): array
    {
        return [];
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        return null;
    }

    public function parsePortions(): int
    {
        return 1;
    }

    public function parseIngredients(): array
    {
        $listItems = $this->xpath->query("//div[@class='entry-content']/ul[1]/li");

        $ingredients = [];
        foreach ($listItems as $item) {
            $ingredients[] = $item->textContent;
        }

        return app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(): array
    {
        $listItems = $this->xpath->query("//div[@class='entry-content']//following::ul/following-sibling::p");

        $steps = [];
        foreach ($listItems as $item) {
            $steps[] = [
                'description' => $item->textContent,
                'image' => '',
            ];
        }

        return $steps;
    }

    public function parseImage(): string
    {
        $imageNode = $this->xpath->query("//div[@class='entry-media-thumb']//@style")->item(0)->nodeValue;
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
