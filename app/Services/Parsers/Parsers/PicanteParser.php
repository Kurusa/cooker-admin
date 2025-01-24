<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMNode;
use DOMXPath;

class PicanteParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//h1[@class='fn']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        return $this->extractCleanSingleValue($xpath, "//ol[@id='wo-breadcrumbs']/li[3]/a/span/text()");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = [];
        $ingredientNodes = $xpath->query("//ul[@class='ingredients']/li");

        foreach ($ingredientNodes as $node) {
            $name = CleanText::cleanText($xpath->query("//span[@class='name']", $node)->item(0)->textContent);
            $amount = CleanText::cleanText($xpath->query("//span[@class='amount']", $node)->item(0)->textContent);

            $ingredients[] = $amount ? "{$name}: {$amount}" : $name;
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $paragraphNodes = $xpath->query("//section[@class='instructions ck-content']/p");

        /** @var DOMNode $paragraphNode */
        foreach ($paragraphNodes as $paragraphNode) {
            $description = CleanText::cleanText($paragraphNode->textContent);
            if (preg_match('/^\d+\)/', $description)) {
                $description = preg_replace('/^\d+\)\s*/', '', $description);
            }

            if ($description) {
                $nextSibling = $xpath->query("following-sibling::p[1]/figure", $paragraphNode);
                $imageUrl = '';
                if ($nextSibling->length > 0) {
                    $imgNode = $xpath->query("//img/@src", $nextSibling->item(0));
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

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query("//img[@class='img-fluid photo result-photo']")->item(0);
        return $imageNode?->getAttribute('src') ?? '';
    }

    public function urlRule(string $url): bool
    {
        return str_contains($url, 'uk/recipes');
    }
}
