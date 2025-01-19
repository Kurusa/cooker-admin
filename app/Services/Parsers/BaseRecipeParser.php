<?php

namespace App\Services\Parsers;

use App\Models\Source;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\Parsers\Formatters\CleanText;
use DOMDocument;
use DOMNode;
use DOMXPath;

abstract class BaseRecipeParser implements RecipeParserInterface
{
    abstract public function urlRule(string $url): bool;

    public function loadHtml(string $url): DOMXPath
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);

        //file_put_contents('storage/logs/html.html', $html);

        return new DOMXPath($dom);
    }

    public function getFilteredSitemapUrls(Source $source): array
    {
        $urls = [];

        foreach ($source->sitemapUrls as $sitemap) {
            $sitemapElements = simplexml_load_file($sitemap->sitemap_url);

            echo 'Processing: ' . $sitemap->sitemap_url . PHP_EOL;

            foreach ($sitemapElements as $sitemapElement) {
                $url = (string) $sitemapElement->loc;
                if ($this->urlRule($url)) {
                    $urls[] = $url;
                }
            }
        }

        return $urls;
    }

    protected function extractCleanSingleValue(DOMXPath $xpath, string $query, ?DOMNode $contextNode = null): string
    {
        $node = $xpath->query($query, $contextNode)->item(0);
        return $node ? CleanText::cleanText($node->nodeValue) : '';
    }

    protected function extractMultipleValues(DOMXPath $xpath, string $query): array
    {
        $nodes = $xpath->query($query);
        $values = [];

        foreach ($nodes as $node) {
            $values[] = CleanText::cleanText($node->nodeValue);
        }

        return $values;
    }

    protected function formatIngredients(array $ingredients): array
    {
        $parsedIngredients = [];
        foreach ($ingredients as $ingredient) {
            $parsedIngredients[] = $this->formatIngredient(CleanText::cleanText($ingredient));
        }

        return $parsedIngredients;
    }

    protected function formatIngredient(string $ingredient): array
    {
        $pattern = '/^(?:(.*?)\s*[-–—:]\s*)?(\d+[.,]?\d*|\d+\/\d+)(?:\s*([^\d]+)?)$/u';

        $title = $ingredient;
        $quantity = null;
        $unit = null;

        if (preg_match($pattern, $ingredient, $matches)) {
            $title = $matches[1];

            if (isset($matches[2])) {
                $cleanQuantity = str_replace(',', '.', CleanText::cleanText($matches[2]));
                if (str_contains('/', $cleanQuantity)) {
                    $numbers = explode('/', $cleanQuantity);
                    $cleanQuantity = round($numbers[0] / $numbers[1], 6);
                }
                $quantity = $cleanQuantity == intval($cleanQuantity) ? intval($cleanQuantity) : $cleanQuantity;
            }

            $unit = $matches[3] ?? null;
        }

        return [
            'title' => CleanText::cleanText($title ?? ''),
            'quantity' => CleanText::cleanText($quantity ?? ''),
            'unit' => CleanText::cleanText($unit ?? ''),
        ];
    }
}
