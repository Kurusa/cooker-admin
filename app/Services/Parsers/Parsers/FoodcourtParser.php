<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CookingTimeFormatter;
use DOMNode;

class FoodcourtParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractCleanSingleValue("//span[@class='breadcrumb_last']");
    }

    public function parseCategories(): array
    {
        return $this->xpathService->extractCleanSingleValue("//p[@id='breadcrumbs']//span[2]/a");
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        $timeNodes = [
            "//div[@class='wp-block-media-text__content']/p/strong[contains(text(), 'час')]/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(text(), 'Час')]/text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Час приготування')]/following-sibling::br/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Час приготування')]/br/following::text()",
        ];

        $totalTime = 0;

        foreach ($timeNodes as $xpathQuery) {
            $nodeList = $this->xpath->query($xpathQuery);
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

    public function parsePortions(): int
    {
        $portionNodes = [
            "//div[@class='wp-block-media-text__content']/p/strong[contains(text(), 'Порції')]/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(text(), 'Кількість порцій')]/text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Кількість порцій')]/following-sibling::br/following-sibling::text()",
            "//div[@class='wp-block-media-text__content']/p[contains(., 'Кількість порцій')]/br/following::text()",
        ];

        foreach ($portionNodes as $xpathQuery) {
            $nodeList = $this->xpath->query($xpathQuery);
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

    public function parseIngredients(bool $debug = false): array
    {
        $ingredientsNodes = [
            "//div[@class='wp-block-media-text__content']/ul[@class='wp-block-list']/li",
            "//div[@class='wp-block-media-text__content']/ul[contains(@class, 'has-medium-font-size')]/li",
            "//div[@class='wp-block-media-text__content']/ul/li",
        ];

        $ingredients = [];

        foreach ($ingredientsNodes as $xpathQuery) {
            $nodeList = $this->xpath->query($xpathQuery);
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

    public function parseSteps(bool $debug = false): array
    {
        return array_map(function (DOMNode $item) use ($xpath) {
            $id = $item->attributes->getNamedItem('id')->textContent;

            $description = $this->xpath->query("//div[@id='$id']/div[@class='fullstory-description']")->item(0)->textContent;
            $image = $this->xpath->query("//div[@id='$id']/div[@class='fullstory-img']/img/@data-src")->item(0)?->textContent;

            return [
                'description' => $description,
                'image'       => $image ? 'https://ua.yabpoela.net' . $image : '',
            ];
        }, iterator_to_array($this->xpath->query("//div[contains(@id,'step-')]")));
    }

    public function parseImage(): string
    {
        return $this->xpath->query("//figure[@class='wp-block-media-text__media']/img/@src")->item(0)?->textContent ?? '';
    }

    public function urlRule(string $url): bool
    {
        return true;
    }
}
