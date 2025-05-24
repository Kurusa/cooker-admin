<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class UaReceptParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        $class = 'recipe-card-title';

        return $this->xpathService->extractCleanSingleValue("//h2[@class='$class']");
    }

    public function parseCategories(): array
    {
        $class = 'yoast-breadcrumbs';

        return $this->xpathService->extractCleanSingleValue("//div[@class='$class']//span[2]/a/text()");
    }

    public function parseComplexity(): Complexity
    {
        $difficultyNode = $this->xpath->query("//li[@class='single-meta category-meta'][contains(text(), 'Difficulty:')]");

        $text = $difficultyNode->item(0)?->textContent ?? '';
        $difficulty = strtolower(trim(str_replace('Difficulty:', '', $text)));

        return Complexity::mapParsedValue($difficulty);
    }

    public function parseCookingTime(): ?int
    {
        $cookingTime = 0;

        $cookingTime += (int)$this->xpathService->extractCleanSingleValue('//div[contains(@class, "detail-item")]/span[text()="Час підготовки"]/following-sibling::p');

        $cookingTime += (int)$this->xpathService->extractCleanSingleValue('//div[contains(@class, "detail-item")]/span[text()="Час приготування"]/following-sibling::p');

        return $cookingTime;
    }

    public function parsePortions(): int
    {
        return (int)$this->xpathService->extractCleanSingleValue('//div[contains(@class, "detail-item")]/span[text()="Порції"]/following-sibling::p');
    }

    public function parseIngredients(): array
    {
        $ingredientNodes = $this->xpathService->extractMultipleValues("//ul[@class='ingredients-list layout-1-column']//li//p/text()");

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredientNodes);
    }

    public function parseSteps(): array
    {
        $stepNodes = $this->xpath->query("//div[contains(@class, 'recipe-card-directions')]//li[contains(@class, 'direction-step')]//p");

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
                    'description' => $description,
                    'imageUrl' => $imageUrl,
                ];
            }
        }

        if (empty($steps)) {
            $stepNodes = $this->xpath->query("//li[contains(@class, 'direction-step')]");

            $steps = [];
            foreach ($stepNodes as $stepNode) {
                $description = $this->xpath->query("//strong", $stepNode)->item(0)->textContent ?? '';
                $imageUrl = $this->xpath->query("//img/@src", $stepNode)->item(0)->textContent ?? '';

                if (mb_strlen($description)) {
                    $steps[] = [
                        'description' => $description,
                        'imageUrl' => $imageUrl,
                    ];
                }
            }
        }

        return array_filter($steps);
    }

    public function parseImage(): string
    {
        $class = 'wpzoom-recipe-card-image';

        $imageNode = $this->xpath->query("//img[@class='$class']")->item(0);
        return $imageNode?->getAttribute('src') ?? '';
    }

    public function isExcludedByUrlRule(string $url): bool
    {
        return true;
    }
}
