<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class RudParser extends BaseRecipeParser
{
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
            'https://rud.ua/consumer/recipe/napitki/toplenoe-moloko-recept',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        $disallowedUrls = [
            'https://rud.ua/consumer/recipe/kadaif/',
            'https://rud.ua/consumer/recipe/drugi-stravy/',
            'https://rud.ua/consumer/recipe/vupechka/',
            'https://rud.ua/consumer/recipe/desertu/',
            'https://rud.ua/consumer/recipe/napitki/',
            'https://rud.ua/consumer/recipe/pershi-stravy/',
            'https://rud.ua/consumer/',
            'https://rud.ua/consumer/recipe/',
            'https://rud.ua/consumer/recipe/tortu/',
            'https://rud.ua/consumer/recipe/salaty-i-zakusky/',
            'https://rud.ua/',
        ];

        if (in_array($url, $disallowedUrls, true)) {
            return true;
        }

        return false;
    }

    public function getSourceKey(): string
    {
        return 'rud';
    }
}
