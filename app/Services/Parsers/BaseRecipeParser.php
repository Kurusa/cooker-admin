<?php

namespace App\Services\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use DOMDocument;
use DOMXPath;

abstract class BaseRecipeParser implements RecipeParserInterface
{
    abstract public function getSitemapUrl(): string;

    public function loadHtml(string $url): DOMXPath
    {
        $html = file_get_contents($url);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    public function getSitemapUrls(): array
    {
        $sitemapUrl = $this->getSitemapUrl();
        $sitemapElements = simplexml_load_file($sitemapUrl);

        if (!$sitemapElements) {
            throw new \RuntimeException("Failed to load sitemap: {$sitemapUrl}");
        }

        $urls = [];
        foreach ($sitemapElements as $sitemapElement) {
            $urls[] = (string) $sitemapElement->loc;
        }

        return $urls;
    }

    protected function cleanText(string $text): ?string
    {
        $text = trim($text);

        $text = mb_strtolower($text);

        $text = rtrim($text, ',');

        $text = rtrim($text, '.');

        $text = rtrim($text, ' -');

        $text = ltrim($text, '-');

        $text = ltrim($text, ':');

        return $text;
    }

    protected function extractSingleValue(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)->item(0);
        return $node ? $this->cleanText($node->nodeValue) : null;
    }

    protected function extractMultipleValues(DOMXPath $xpath, string $query): array
    {
        $nodes = $xpath->query($query);
        $values = [];

        foreach ($nodes as $node) {
            $values[] = $this->cleanText($node->nodeValue);
        }

        return $values;
    }

    protected function formatCookingTime(?string $time): ?int
    {
        if (!$time) {
            return null;
        }

        $time = str_replace(['хвилин', 'хвилина', 'години', 'година'], '', mb_strtolower($time));
        $time = trim($time);

        if (preg_match('/(\d+)\s*год/i', $time, $matches)) {
            return (int) $matches[1] * 60;
        }

        return (int) $time;
    }

    protected function formatPortions(?string $portions): ?int
    {
        if (!$portions) {
            return null;
        }

        $portions = str_replace(['порцій', 'порція'], '', mb_strtolower($portions));
        return (int) trim($portions);
    }

    protected function formatComplexity(string $complexity): Complexity
    {
        return Complexity::mapParsedValue($complexity);
    }

    protected function parseIngredient(string $ingredient): array
    {
        $pattern = '/^(.*?)\s*[-–—:]\s*((?:\d+[.,]?\d*|\d+\/\d+)(?:-\d+[.,]?\d*|\d+\/\d+)?)\s*([^\d]+)?$/u';

        $title = trim($ingredient);
        $quantity = null;
        $unit = null;

        if (preg_match($pattern, $ingredient, $matches)) {
            $title = $this->cleanText($matches[1]);

            if (isset($matches[2])) {
                $cleanQuantity = str_replace(',', '.', $this->cleanText($matches[2]));
                if (str_contains('/', $cleanQuantity)) {
                    $numbers = explode('/', $cleanQuantity);
                    $cleanQuantity = round($numbers[0] / $numbers[1], 6);
                }
                $quantity = $cleanQuantity == intval($cleanQuantity) ? intval($cleanQuantity) : $cleanQuantity;
            }

            $unit = isset($matches[3]) ? $this->cleanText($matches[3]) : null;
        }

        return [
            'title' => $title,
            'quantity' => $quantity,
            'unit' => $unit,
        ];
    }

    protected function formatIngredients(array $ingredients): array
    {
        $parsedIngredients = [];
        foreach ($ingredients as $ingredient) {
            $parsedIngredients[] = $this->parseIngredient($ingredient);
        }
        return $parsedIngredients;
    }
}
