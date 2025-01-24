<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMNode;
use DOMXPath;

class UaReceptParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'recipe-card-title';

        return $this->extractCleanSingleValue($xpath, "//h2[@class='$class']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        $class = 'yoast-breadcrumbs';

        return $this->extractCleanSingleValue($xpath, "//div[@class='$class']//span[2]/a/text()");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        $difficultyNode = $xpath->query("//li[@class='single-meta category-meta'][contains(text(), 'Difficulty:')]");

        $text = $difficultyNode->item(0)?->textContent ?? '';
        $difficulty = strtolower(trim(str_replace('Difficulty:', '', $text)));

        return Complexity::mapParsedValue($difficulty);
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $cookingTime = 0;

        $cookingTime += (int) $this->extractCleanSingleValue($xpath, '//div[contains(@class, "detail-item")]/span[text()="Час підготовки"]/following-sibling::p');

        $cookingTime += (int) $this->extractCleanSingleValue($xpath, '//div[contains(@class, "detail-item")]/span[text()="Час приготування"]/following-sibling::p');

        return $cookingTime;
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return (int) $this->extractCleanSingleValue($xpath, '//div[contains(@class, "detail-item")]/span[text()="Порції"]/following-sibling::p');
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredientNodes = $this->extractMultipleValues($xpath, "//ul[@class='ingredients-list layout-1-column']//li//p/text()");

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredientNodes);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $stepNodes = $xpath->query("//div[contains(@class, 'recipe-card-directions')]//li[contains(@class, 'direction-step')]//p");

        /** @var DOMNode $stepNode */
        foreach ($stepNodes as $stepNode) {
            $description = $stepNode->textContent ?? '';
            $nextSibling = $stepNode->nextSibling;
            $imageUrl = '';
            if ($nextSibling && $nextSibling->nodeName == 'img') {
                $imageUrl = $nextSibling->attributes->getNamedItem('src')->textContent ?? '';
            }

            if ($description) {
                $steps[] = [
                    'description' => CleanText::cleanText($description),
                    'imageUrl' => $imageUrl,
                ];
            }
        }

        if (empty($steps)) {
            $stepNodes = $xpath->query("//li[contains(@class, 'direction-step')]");

            $steps = [];
            foreach ($stepNodes as $stepNode) {
                $description = $xpath->query("//strong", $stepNode)->item(0)->textContent ?? '';
                $imageUrl = $xpath->query("//img/@src", $stepNode)->item(0)->textContent ?? '';

                if (mb_strlen($description)) {
                    $steps[] = [
                        'description' => CleanText::cleanText($description),
                        'imageUrl' => $imageUrl,
                    ];
                }
            }
        }

        return array_filter($steps);
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $class = 'wpzoom-recipe-card-image';

        $imageNode = $xpath->query("//img[@class='$class']")->item(0);
        return $imageNode?->getAttribute('src') ?? '';
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
