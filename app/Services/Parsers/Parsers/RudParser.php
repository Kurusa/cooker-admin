<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMXPath;

class RudParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return CleanText::cleanText($xpath->query("//h2[@itemprop='name']")?->item(0)?->nodeValue ?? '');
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        return CleanText::cleanText($xpath->evaluate("string((//div[@class='wrapper']//span[@itemprop='itemListElement']//span[@itemprop='name'])[last()-1])"));
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        $complexity = mb_strtolower(trim($xpath->evaluate("string((//div[@class='top'])[4]/span[2])")));

        return Complexity::mapParsedValue($complexity);
    }

    public function parseCookingTime(DOMXPath $xpath): int
    {
        $timeText = $xpath->evaluate("string(//div[@class='top']/span[1]/time[@itemprop='cookTime prepTime'])");

        return CookingTimeFormatter::formatCookingTime($timeText);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = array_map(
            fn($row) => CleanText::cleanText($xpath->evaluate("string(.//td[1])", $row) . ':' . $xpath->evaluate("string(.//td[2])", $row)),
            iterator_to_array($xpath->query("//tr[@itemprop='recipeIngredient']"))
        );

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $nodes = $xpath->query("//p[strong[contains(text(), 'Етап')]]");

        $stepNumber = 1;
        foreach ($nodes as $node) {
            $newStepNumber = (int) substr($node->textContent, -1);
            if ($newStepNumber >= $stepNumber) {
                $steps[] = CleanText::cleanText($node->nextSibling->textContent);
                $stepNumber = $newStepNumber;
            } else {
                break;
            }
        }

        if (count($steps)) {
            return $steps;
        }

        $firstNode = $xpath->query("//p[@itemprop='recipeInstructions']")->item(0);
        $currentNode = $firstNode->nextSibling;

        while ($currentNode) {
            if ($currentNode->nodeType === XML_ELEMENT_NODE) {
                if ($currentNode->nodeName === 'p') {
                    $steps[] = CleanText::cleanText($currentNode->textContent);
                }

                if ($currentNode->nodeName === 'ol' || $currentNode->nodeName === 'ul') {
                    foreach ($currentNode->childNodes as $liNode) {
                        if ($liNode->nodeName === 'li') {
                            $steps[] = CleanText::cleanText($liNode->textContent);
                        }
                    }
                }
            }
            $currentNode = $currentNode->nextSibling;
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $src = $xpath->evaluate("string(//p[@itemprop='recipeInstructions']/preceding-sibling::img[1]/@src)");
        return $src ? 'https://rud.ua' . trim($src) : '';
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/brands',
            '/products',
            '/company',
            '/frozen-vegetables',
            '/ru/',
            '/en/',
            '/es/',
            '/distributors/',
            '/horeca/',
            '/excursions/',
            '/contacts/',
            '/tenders/',
            '/press-center/',
            '/articles/',
            '/zdorova-yizha/',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
