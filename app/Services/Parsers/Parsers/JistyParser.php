<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMNode;
use DOMXPath;
use Exception;

class JistyParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        $class = 'post-title mb-30';
        if ($this->hasMoreThanOneRecipeOnPage($xpath, $class)) {
            throw new Exception('There is more than one recipe on page');
        }

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

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
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

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query(".//figure[@class='aligncenter size-large']/img")->item(0);
        if (!$imageNode) {
            $imageNode = $xpath->query(".//div[@class='thumbnail text-center mb-20']/img")->item(0);
        }

        if ($imageSrc = $imageNode?->getAttribute('data-src')) {
            return 'https://jisty.com.ua' . $imageSrc;
        }

        return '';
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
            // продебажити
            'https://jisty.com.ua/kartoplya-z-sirom-na-garnir/',
            'https://jisty.com.ua/ovochevij-chizburger/',
            'https://jisty.com.ua/nokki-abo-sekretna-zbroya-proti-italijskih-mafiozi/',
            'https://jisty.com.ua/sirnij-kreker/',
            'https://jisty.com.ua/kavovij-liker/',
            // кілька рецептів на сторінці
            'retseptiv',
            'retsepty',
            'variantiv',
            'stravy',
            'reczepty',
            'https://jisty.com.ua/pecheritsi-marinovani-nashvidkuruch/',
            'https://jisty.com.ua/reczepty-smachnyh-strav-gruzynskoyi-kuhni-dlya-spravzhnih-gurmaniv/',
            'yaki',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }

    private function hasMoreThanOneRecipeOnPage(DOMXPath $xpath, string $class): bool
    {
        return $xpath->query(".//h1[@class='{$class}']")->length > 1 ||
            $xpath->query(".//h2[@class='has-text-align-center wp-block-heading']")->length > 1;
    }
}
