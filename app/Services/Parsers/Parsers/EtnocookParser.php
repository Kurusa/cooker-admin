<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMNode;

class EtnocookParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractSingleMetaAttribute('og:title');
    }

    public function parseCategories(): array
    {
        return $this->xpathService->extractMultipleValues("//meta[@property='article:section']", true);
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::mapParsedValue(
            str_replace(
                'складність: ',
                '',
                $this->xpathService
                    ->extractCleanSingleValue("//p[contains(normalize-space(),'Складність')]")
            )
        );
    }

    public function parseCookingTime(): ?int
    {
        $cookingTime = $this->xpathService->extractCleanSingleValue("//p[contains(., 'Час приготування')]");

        return CookingTimeFormatter::formatCookingTime($cookingTime);
    }

    public function parsePortions(): int
    {
        return 1;
    }

    public function parseImage(): string
    {
        return $this->xpathService->extractSingleMetaAttribute('og:image');
    }

    public function parseIngredients(bool $debug = false): array
    {
        $ingredients = [];

        array_map(function (DOMNode $item) use (&$ingredients) {
            $exploded = explode("<br>", $item->textContent);

            foreach ($exploded as $ingredient) {
                $ingredients[] = CleanText::cleanText($ingredient);
            }
        }, iterator_to_array($this->xpath->query("//p[contains(.,'………')]")));

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(bool $debug = false): array
    {
        $steps = [];

        array_map(function (DOMNode $item) use (&$steps) {
            $exploded = array_filter(preg_split('/[0-9]+\./', $item->textContent));

            foreach ($exploded as $step) {
                $steps[] = [
                    'description' => CleanText::cleanText($step),
                    'image' => '',
                ];
            }
        }, iterator_to_array($this->xpath->query("//p[contains(.,'Спосіб приготування:')]/following-sibling::p")));

        return $debug ? $steps : app(DeepseekService::class)->parseSteps($steps);
    }


    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/tag/',
            'porada-dnya',
            'yak-',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
