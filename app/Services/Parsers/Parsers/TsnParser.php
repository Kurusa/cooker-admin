<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\BaseRecipeParser;
use DOMXPath;

class TsnParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, "//h1[@class='c-card__title']//span") ?? '';
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
        $timeNode = $xpath->query("//dd[contains(@class, 'i-cooking-time')]");

        if (!$timeNode->length) {
            return null;
        }

        $timeText = trim($timeNode->item(0)->textContent);

        $totalMinutes = 0;

        if (preg_match('/(\d+)\s*(година|години|год)/iu', $timeText, $matches)) {
            $totalMinutes += (int) $matches[1] * 60;
        }

        if (preg_match('/(\d+)\s*(хвилина|хвилини|хв)/iu', $timeText, $matches)) {
            $totalMinutes += (int) $matches[1];
        }

        return $totalMinutes;
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];
        $ingredientNodes = $xpath->query("//div[@class='c-bar c-bar--normal c-bar--dense c-bar--log c-bar--y-divided c-bar--unordered c-bar--label-px0']/dl");

        foreach ($ingredientNodes as $node) {
            $nameNode = $xpath->query(".//dt", $node);
            $quantityNode = $xpath->query(".//dd", $node);

            $name = trim($nameNode->item(0)?->textContent ?? '');
            $quantity = trim($quantityNode->item(0)?->textContent ?? '');

            $rawIngredient = $name . ': ' . $quantity;

            $ingredients[] = $this->formatIngredient($rawIngredient);
        }

        if (empty($ingredients)) {
            $ingredientHeading = $xpath->query("//h2[strong[text()='Інгредієнти:']]");

            if ($ingredientHeading->length > 0) {
                $nextElement = $ingredientHeading->item(0)->nextSibling;

                while ($nextElement && $nextElement->nodeName !== 'ul') {
                    $nextElement = $nextElement->nextSibling;
                }

                if ($nextElement && $nextElement->nodeName === 'ul') {
                    $listItems = $xpath->query(".//li", $nextElement);

                    foreach ($listItems as $item) {
                        $ingredients[] = [
                            'title' => $this->cleanText($item->textContent),
                        ];
                    }
                }
            }
        }

        return $ingredients;
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $stepNodes = $xpath->query("//div[@data-content='']/ol/li");
        foreach ($stepNodes as $node) {
            $steps[] = $this->cleanText($node->textContent);
        }

        if (empty($steps)) {
            $paragraphNodes = $xpath->query("//div[@data-content='']/p");
            foreach ($paragraphNodes as $node) {
                $text = $this->cleanText($node->textContent);

                if (preg_match('/^\d+\./', $text)) {
                    $text = preg_replace('/^\d+\.\s*/', '', $text);
                    $steps[] = $text;
                }
            }
        }

        if (empty($steps)) {
            $paragraphNodes = $xpath->query("//div[@data-content='']/p");
            $isStepsStarted = false;

            foreach ($paragraphNodes as $node) {
                $text = $this->cleanText($node->textContent);
                if (!$isStepsStarted && stripos($text, 'приготування') !== false) {
                    $isStepsStarted = true;
                    continue;
                }

                if ($isStepsStarted && !empty($text) && (
                        $text !== 'смачного!' &&
                        $text !== 'читайте також'
                    )
                ) {
                    $steps[] = $text;
                }
            }
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query("//img[@class='c-card__embed__img']");
        $src = $imageNode->item(0)->getAttribute('src');
        return trim($src);
    }

    public function urlRule(string $url): bool
    {
        return str_contains($url, 'recepty');
    }
}
