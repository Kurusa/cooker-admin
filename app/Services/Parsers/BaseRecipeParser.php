<?php

namespace App\Services\Parsers;

use App\Models\Recipe;
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

        foreach ($source->sitemaps as $sitemap) {
            $sitemapElements = simplexml_load_file($sitemap->url);

            foreach ($sitemapElements as $sitemapElement) {
                $url = (string) $sitemapElement->loc;
                if ($this->urlRule($url) && !Recipe::where('source_url', $url)->exists()) {
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
}
