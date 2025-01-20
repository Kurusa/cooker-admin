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
        return $this->extractCleanSingleValue($xpath, "//h2[@itemprop='name']") ?? '';
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

    public function parseCookingTime(DOMXPath $xpath): int
    {
        $timeNode = $xpath->query("//div[@class='top']/span[1]/time[@itemprop='cookTime prepTime']");

        $timeText = $timeNode->item(0)->textContent;

        return CookingTimeFormatter::formatCookingTime($timeText);
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];

        $rows = $xpath->query("//tr[@itemprop='recipeIngredient']");
        foreach ($rows as $row) {
            $ingredientNameNode = $xpath->query(".//td[1]", $row);
            $ingredientQuantityNode = $xpath->query(".//td[2]", $row);

            $ingredients[] = implode(':', [
                $ingredientNameNode->item(0)?->textContent ?? '',
                $ingredientQuantityNode->item(0)?->textContent ?? ''
            ]);
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
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
        $imageNode = $xpath->query("//p[@itemprop='recipeInstructions']/preceding-sibling::img[1]");

        if ($imageNode->length > 0) {
            $src = $imageNode->item(0)->getAttribute('src');
            return 'https://rud.ua' . trim($src);
        }

        return '';
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
