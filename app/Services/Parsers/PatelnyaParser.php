<?php

namespace App\Services\Parsers;

use App\Enums\Recipe\Complexity;
use App\Models\Source;
use DOMXPath;

class PatelnyaParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, ".//h1[@class='p-name name-title fn']") ?? '';
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return $this->extractSingleValue($xpath, ".//div[@class='title-detail']/a/span");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        $rawComplexity = $this->extractSingleValue($xpath, ".//div[i/span[contains(text(), 'Рівень складності:')]]/i/span[@class='color-414141']");
        return $this->formatComplexity($rawComplexity);
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $rawTime = $this->extractSingleValue($xpath, ".//div[i[@class='duration']]/i/span[@class='color-414141 value-title']");
        return $this->formatCookingTime($rawTime);
    }

    public function parsePortions(DOMXPath $xpath): ?int
    {
        $rawPortions = $this->extractSingleValue($xpath, ".//div[i/span[contains(text(), 'Кількість порцій:')]]/i/span[@class='color-414141 yield']");
        return $this->formatPortions($rawPortions);
    }

    public function parseIngredients(DOMXPath $xpath): array
    {
        $rawIngredients = $this->extractMultipleValues($xpath, ".//div[@class='list-ingredient old-list']//ul[@class='ingredient']/li");
        return $this->formatIngredients($rawIngredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];

        $listItems = $xpath->query(".//div[@class='e-instructions step-instructions instructions']//ol/li");
        if ($listItems->length > 0) {
            foreach ($listItems as $item) {
                $steps[] = $item->textContent;
            }
        }

        $paragraphs = $xpath->query(".//div[@class='e-instructions step-instructions instructions']/p");
        if ($paragraphs->length > 0) {
            foreach ($paragraphs as $index => $paragraph) {
                if ($index === 0) {
                    continue;
                }

                $steps[] = substr($paragraph->textContent, 3);
            }
        }

        return array_filter(array_map(function ($step) {
            return preg_replace('/[^\PC\s]/u', '', $step);
        }, $steps));
    }

    public function parseImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//img[contains(@class, 'article-img-left')]")->item(0);
        return $imageNode ? trim($imageNode->getAttribute('src')) : null;
    }

    public function getSitemapUrl(): string
    {
        return 'https://patelnya.com.ua/post-sitemap.xml';
    }

    public function getSource(): Source
    {
        return Source::where('url', 'https://patelnya.com.ua')->first();
    }
}
