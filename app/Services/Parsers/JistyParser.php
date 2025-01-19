<?php

namespace App\Services\Parsers;

use App\Enums\Recipe\Complexity;
use DOMXPath;

class JistyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, ".//h1[@class='post-title mb-30']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, ".//span[@class='post-cat bg-warning']") ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        return 0;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $rawIngredients = $this->extractMultipleValues($xpath, "//ul[@class='ingredients-list']/li");

        return $this->formatIngredients($rawIngredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = $xpath->query("//ul[@class='directions-list']/li[@class='direction-step']");

        $result = [];

        foreach ($steps as $step) {
            $description = trim($step->textContent);

            $imageNode = $xpath->query(".//img", $step);
            $imageUrl = 'https://jisty.com.ua' . $imageNode->item(0)?->getAttribute('data-src');

            $result[] = [
                'description' => $description,
                'image_url' => $imageUrl,
            ];
        }

        return $result;
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//figure[@class='aligncenter size-large']/img")->item(0);
        if (!$imageNode) {
            $imageNode = $xpath->query(".//div[@class='thumbnail text-center mb-20']/img")->item(0);
        }

        $src = trim($imageNode?->getAttribute('data-src'));

        return 'https://jisty.com.ua' . $src;
    }

    protected function formatIngredients(array $ingredients): array
    {
        $parsedIngredients = [];
        foreach ($ingredients as $ingredient) {
            $ingredient = str_replace('(adsbygoogle=window.adsbygoogle||[]).push({})', '', $ingredient);
            $ingredient = str_replace('спеції: ', '', $ingredient);
            $parsedIngredients[] = $this->formatIngredient($ingredient);
        }

        return $parsedIngredients;
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
