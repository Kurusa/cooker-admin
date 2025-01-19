<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\BaseRecipeParser;
use DOMXPath;

class RudParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, "//h2[@itemprop='name']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $categories = $this->extractMultipleValues($xpath, "//div[@class='wrapper']//span[@itemprop='itemListElement']//span[@itemprop='name']");

        return $categories[count($categories) - 2] ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        $nodes = $xpath->query("(//div[@class='top'])[4]/span");
        $complexity = mb_strtolower(trim($nodes->item(1)->textContent));

        return Complexity::mapParsedValue($complexity);
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $timeNode = $xpath->query("//div[@class='top']/span[1]/time[@itemprop='cookTime prepTime']");

        $timeText = trim($timeNode->item(0)->textContent);

        if (preg_match('/(\d+)\s*(година|години|годин)/iu', $timeText, $matches)) {
            return (int) $matches[1] * 60;
        } elseif (preg_match('/(\d+)\s*(хвилина|хвилини|хв)/iu', $timeText, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];
        $rows = $xpath->query("//tr[@itemprop='recipeIngredient']");

        foreach ($rows as $row) {
            $ingredientNameNode = $xpath->query(".//td[1]", $row);
            $ingredientName = trim($ingredientNameNode->item(0)?->textContent ?? '');

            $ingredientQuantityNode = $xpath->query(".//td[2]", $row);
            $ingredientQuantity = trim($ingredientQuantityNode->item(0)?->textContent ?? '');

            $rawIngredient = $ingredientName . ': ' . $ingredientQuantity;

            $ingredients[] = $this->formatIngredient($rawIngredient);
        }

        return $ingredients;
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $nodes = $xpath->query("//p[strong[contains(text(), 'Етап')]]");

        foreach ($nodes as $node) {
            $descriptionNode = $node->nextSibling;
            $description = $descriptionNode ? trim($descriptionNode->textContent) : '';

            $steps[] = $description;
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query("//p[@itemprop='recipeInstructions']/preceding-sibling::img[1]");

        if ($imageNode->length > 0) {
            $src = $imageNode->item(0)->getAttribute('src');
            return 'https://rud.ua' . trim($src);
        }

        return null;
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
