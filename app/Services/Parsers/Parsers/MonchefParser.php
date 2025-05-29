<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class MonchefParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//article[contains(@class, 'post type-post status-publish format-standard')]")->item(0);

        $imageNode = $this->xpath->query("//div[contains(@class, 'entry-media')]")?->item(0);
        if ($imageNode) {
            $clone = $imageNode->cloneNode(true);
            $recipeNode->insertBefore($clone, $recipeNode->firstChild);
        }

        return $recipeNode;
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
