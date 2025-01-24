<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMNode;
use DOMXPath;

class JistyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'post-title mb-30';

        return $this->extractCleanSingleValue($xpath, "//h1[@class='$class']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        $class = 'post-cat bg-warning';

        return $this->extractCleanSingleValue($xpath, "//span[@class='$class']");
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
        $rawIngredients = $this->extractMultipleValues(
            $xpath,
            ".//div[@class='wp-block-wpzoom-recipe-card-block-ingredients'][1]/ul[@class='ingredients-list']/li"
        );

        $ingredients = [];
        foreach ($rawIngredients as $ingredient) {
            $ingredient = str_replace(['(adsbygoogle=window.adsbygoogle||[]).push({})', 'спеції:'], '', $ingredient);
            $ingredients[] = CleanText::cleanText($ingredient);
        }

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $stepNodes = $xpath->query("//div[@class='wp-block-wpzoom-recipe-card-block-directions'][1]/ul[@class='directions-list']/li[@class='direction-step']");

        $steps = [];

        /** @var DOMNode $stepNode */
        foreach ($stepNodes as $stepNode) {
            $imageNode = $xpath->query("//img", $stepNode);
            $imageUrl = $imageNode->item(0)?->getAttribute('data-src') ? 'https://jisty.com.ua' . $imageNode->item(0)->getAttribute('data-src') : '';

            $description = CleanText::cleanText($stepNode->textContent);
            if (!in_array($description, array_column($steps, 'description'))) {
                $steps[] = [
                    'description' => $description,
                    'image_url' => $imageUrl,
                ];
            }
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query(
            ".//figure[@class='aligncenter size-large']/img | .//div[@class='thumbnail text-center mb-20']/img"
        )->item(0);

        $imageSrc = $imageNode?->getAttribute('data-src');
        return $imageSrc ? 'https://jisty.com.ua' . $imageSrc : '';
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/blog',
            '/top-',
            '-faktiv-',
            '-pravil-',
            '-productiv-',
            'restoran-',
            'kuhar',
            'najsmachnishih-retseptiv-mlintsiv',
            'yak-pr',
            'najkrash'
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
