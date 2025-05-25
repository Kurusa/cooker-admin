<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class RudParser extends BaseRecipeParser
{
    public function isExcludedByUrlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/brands',
            '/products',
            '/company',
            '/frozen-vegetables',
            '/ru/',
            '/en/',
            '/es/',
            '/distributors/',
            '/horeca/',
            '/excursions/',
            '/contacts/',
            '/tenders/',
            '/press-center/',
            '/articles/',
            '/zdorova-yizha/',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public function extractRecipeNode(): DOMNode
    {
        $nodes = $this->xpath->query("//div[contains(@class, 'item clearfix')]");
        $wrapper = $this->xpath->document->createElement('div');

        foreach ($nodes as $node) {
            $wrapper->appendChild($node->cloneNode(true));
        }

        return $wrapper;
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
