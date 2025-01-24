<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMNode;
use DOMXPath;

class FoodcourtParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//span[@class='breadcrumb_last']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        return $this->extractCleanSingleValue($xpath, "//p[@id='breadcrumbs']//span[2]/a");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $timeNodes = [
            "//div[@class='wp-block-media-text__content']/p/strong[contains(text(), 'час')]/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(text(), 'Час')]/text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Час приготування')]/following-sibling::br/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Час приготування')]/br/following::text()",
        ];

        $totalTime = 0;

        foreach ($timeNodes as $xpathQuery) {
            $nodeList = $xpath->query($xpathQuery);
            if ($nodeList !== false && $nodeList->length > 0) {
                foreach ($nodeList as $node) {
                    $cleanTime = trim($node->textContent);
                    if (!empty($cleanTime)) {
                        $totalTime += CookingTimeFormatter::formatCookingTime($cleanTime);
                    }
                }
            }
        }

        return $totalTime > 0 ? $totalTime : null;
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        $portionNodes = [
            "//div[@class='wp-block-media-text__content']/p/strong[contains(text(), 'Порції')]/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(text(), 'Кількість порцій')]/text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Кількість порцій')]/following-sibling::br/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Кількість порцій')]/br/following::text()",
        ];

        foreach ($portionNodes as $xpathQuery) {
            $nodeList = $xpath->query($xpathQuery);
            if ($nodeList !== false && $nodeList->length > 0) {
                foreach ($nodeList as $node) {
                    if (!empty($node->textContent)) {
                        return (int) $node->textContent;
                    }
                }
            }
        }

        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredientsNodes = [
            "//div[@class='wp-block-media-text__content']/ul[@class='wp-block-list']/li",
            "//div[@class='wp-block-media-text__content']/ul[contains(@class, 'has-medium-font-size')]/li",
            "//div[@class='wp-block-media-text__content']/ul/li",
        ];

        $ingredients = [];

        foreach ($ingredientsNodes as $xpathQuery) {
            $nodeList = $xpath->query($xpathQuery);
            if ($nodeList !== false && $nodeList->length > 0) {
                foreach ($nodeList as $node) {
                    $ingredient = trim($node->textContent);
                    if (!empty($ingredient)) {
                        $ingredients[] = $ingredient;
                    }
                }
            }
        }

        $ingredients = array_unique($ingredients);

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return array_map(function (DOMNode $item) use ($xpath) {
            $id = $item->attributes->getNamedItem('id')->textContent;

            $description = $xpath->query("//div[@id='$id']/div[@class='fullstory-description']")->item(0)->textContent;
            $image = $xpath->query("//div[@id='$id']/div[@class='fullstory-img']/img/@data-src")->item(0)?->textContent;

            return [
                'description' => $description,
                'image' => $image ? 'https://ua.yabpoela.net' . $image : '',
            ];
        }, iterator_to_array($xpath->query("//div[contains(@id,'step-')]")));
    }

    public function parseImage(DOMXPath $xpath): string
    {
        return $xpath->query("//figure[@class='wp-block-media-text__media']/img/@src")->item(0)?->textContent ?? '';
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
