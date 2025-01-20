<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use App\Services\Parsers\Formatters\IngredientFormatter;
use DOMNode;
use DOMXPath;

class JistyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'post-title mb-30';

        return $this->extractCleanSingleValue($xpath, ".//h1[@class='$class']");
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        $class = 'post-cat bg-warning';

        return $this->extractCleanSingleValue($xpath, ".//span[@class='$class']");
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
        $rawIngredients = $this->extractMultipleValues($xpath, "//ul[@class='ingredients-list']/li");

        $parsedIngredients = [];
        foreach ($rawIngredients as $ingredient) {
            $ingredient = str_replace('(adsbygoogle=window.adsbygoogle||[]).push({})', '', $ingredient);
            $ingredient = str_replace('спеції: ', '', $ingredient);

            $parsedIngredients[] = CleanText::cleanText($ingredient);
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($parsedIngredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $stepNodes = $xpath->query("//ul[@class='directions-list']/li[@class='direction-step']");

        $steps = [];
        $descriptions = [];

        /** @var DOMNode $stepNode */
        foreach ($stepNodes as $stepNode) {
            $imageNode = $xpath->query(".//img", $stepNode);
            $imageUrl = '';
            if ($imageSrc = $imageNode->item(0)?->getAttribute('data-src')) {
                $imageUrl = 'https://jisty.com.ua' . $imageSrc;
            }

            $description = CleanText::cleanText($stepNode->textContent);
            if (!isset($descriptions[$description])) {
                $steps[] = [
                    'description' => $description,
                    'image_url' => $imageUrl,
                ];
                $descriptions[$description] = true;
            }
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//figure[@class='aligncenter size-large']/img")->item(0);
        if (!$imageNode) {
            $imageNode = $xpath->query(".//div[@class='thumbnail text-center mb-20']/img")->item(0);
        }

        return 'https://jisty.com.ua' . $imageNode?->getAttribute('data-src');
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
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
