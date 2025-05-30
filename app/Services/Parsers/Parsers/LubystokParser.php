<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class LubystokParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $headerNode = $this->xpath->query("//div[@class='container top-space-min']")?->item(0);
        $contentNode = $this->xpath->query("//div[@class='container bottom-space-min']")?->item(0);

        $wrapper = $this->xpath->document->createElement('div');
        $wrapper->setAttribute('class', 'recipe-wrapper');

        if ($headerNode) {
            $wrapper->appendChild($headerNode->cloneNode(true));
        }

        if ($contentNode) {
            $wrapper->appendChild($contentNode->cloneNode(true));
        }

        $unwantedXpaths = [
            ".//ul[contains(@class, 'share-buttons')]",
        ];

        foreach ($unwantedXpaths as $xpath) {
            $nodes = $this->xpath->query($xpath, $wrapper);

            foreach (iterator_to_array($nodes) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        return $wrapper;
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
