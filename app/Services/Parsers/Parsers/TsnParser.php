<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CookingTimeFormatter;

class TsnParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractCleanSingleValue("//h1[@class='c-card__title']//span");
    }

    public function parseCategories(): array
    {
        return $this->xpathService->extractCleanSingleValue("//span[@class='post-cat bg-warning']");
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        $timeNode = $this->xpath->query("//dd[contains(@class, 'i-cooking-time')]");

        if (!$timeNode->length) {
            return null;
        }

        $timeText = trim($timeNode->item(0)->textContent);

        return CookingTimeFormatter::formatCookingTime($timeText);
    }

    public function parsePortions(): int
    {
        return 1;
    }

    public function parseIngredients(bool $debug = false): array
    {
        $class = 'c-bar c-bar--normal c-bar--dense c-bar--log c-bar--y-divided c-bar--unordered c-bar--label-px0';
        $ingredientNodes = $this->xpath->query("//div[@class='$class']/dl//dt | //h2[strong[text()='Інгредієнти:']]/following-sibling::ul//li");

        $ingredients = [];

        foreach ($ingredientNodes as $node) {
            if ($node->nodeName === 'dt') {
                $name = $node->textContent;
                $quantityNode = $node->nextSibling;
                while ($quantityNode && $quantityNode->nodeName !== 'dd') {
                    $quantityNode = $quantityNode->nextSibling;
                }
                $quantity = $quantityNode ? $quantityNode->textContent : '';
                $ingredients[] = $name . ':' . $quantity;
            } elseif ($node->nodeName === 'li') {
                $ingredients[] = $node->textContent;
            }
        }

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(bool $debug = false): array
    {
        $steps = [];
        $stepNodes = $this->xpath->query("//div[@data-content='']/ol/li");
        foreach ($stepNodes as $node) {
            $steps[] = $node->textContent;
        }

        if (empty($steps)) {
            $paragraphNodes = $this->xpath->query("//h2[contains(text(), 'Інгредієнти')]/following::ul[1]/li");
            foreach ($paragraphNodes as $node) {
                $steps[] = $node->textContent;
            }
        }

        if (empty($steps)) {
            $paragraphNodes = $this->xpath->query("//div[@data-content='']/p");
            foreach ($paragraphNodes as $node) {
                $text = $node->textContent;

                if (preg_match('/^\d+\./', $text)) {
                    $text = preg_replace('/^\d+\.\s*/', '', $text);
                    $steps[] = $text;
                }
            }
        }

        if (empty($steps)) {
            $paragraphNodes = $this->xpath->query("//div[@data-content='']/p");
            $isStepsStarted = false;

            foreach ($paragraphNodes as $node) {
                $text = $node->textContent;
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

    public function parseImage(): string
    {
        $imageNode = $this->xpath->query("//img[@class='c-card__embed__img']");

        return $imageNode->item(0)->getAttribute('src');
    }

    public function urlRule(string $url): bool
    {
        return str_contains($url, 'recepty');
    }
}
