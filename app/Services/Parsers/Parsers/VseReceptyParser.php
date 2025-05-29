<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;

class VseReceptyParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'type-ranna_recipe')]")->item(0);

        $unwantedXpaths = [
            ".//div[contains(@class, 'tag-share')]",
            ".//div[contains(@class, 'recipe-author')]",
            ".//div[contains(@class, 'related-post')]",
        ];

        foreach ($unwantedXpaths as $xpath) {
            $nodes = $this->xpath->query($xpath, $recipeNode);

            foreach (iterator_to_array($nodes) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        return $recipeNode;
    }

    public function isExcludedByCategory(string $url): bool
    {
        try {
            $html = file_get_contents($url);
        } catch (Exception $e) {
            return true;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $descriptionDiv = $xpath->query("//div[contains(@class, 'item-description')]")->item(0);

        if ($descriptionDiv) {
            $paragraphs = $xpath->query('./p', $descriptionDiv);

            return $paragraphs->length === 1;
        }

        return false;
    }
}
