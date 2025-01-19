<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\BaseRecipeParser;
use DOMNode;
use DOMXPath;

class PicanteParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, ".//h1[@class='fn']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, ".//ol[@id='wo-breadcrumbs']/li[3]/a/span/text()") ?? '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];
        $ingredientNodes = $xpath->query("//ul[@class='ingredients']/li");

        foreach ($ingredientNodes as $node) {
            $name = $this->cleanText($xpath->query(".//span[@class='name']", $node)->item(0)->textContent);
            $amount = $this->cleanText($xpath->query(".//span[@class='amount']", $node)->item(0)->textContent);

            $ingredient = $amount ? "{$name}: {$amount}" : $name;
            $ingredients[] = $this->formatIngredient($ingredient);
        }

        return $ingredients;
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $paragraphNodes = $xpath->query("//section[@class='instructions ck-content']/p");

        /**
         * @var int $index
         * @var DOMNode $paragraphNode
         */
        foreach ($paragraphNodes as $paragraphNode) {
            $description = $this->cleanText($paragraphNode->textContent);
            if (preg_match('/^\d+\)/', $description)) {
                $description = preg_replace('/^\d+\)\s*/', '', $description);
            }

            if ($description) {
                $nextSibling = $xpath->query("following-sibling::p[1]/figure", $paragraphNode);
                $imageUrl = '';
                if ($nextSibling->length > 0) {
                    $imgNode = $xpath->query(".//img/@src", $nextSibling->item(0));
                    $imageUrl = $imgNode->item(0)->nodeValue;
                }

                $steps[] = [
                    'description' => $description,
                    'imageUrl' => 'https://picantecooking.com' . $imageUrl,
                ];
            }
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//img[@class='img-fluid photo result-photo']")->item(0);
        return $imageNode?->getAttribute('src');
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
