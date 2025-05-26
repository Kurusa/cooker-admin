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

    public function getSourceKey(): string
    {
        return 'rud';
    }
}
