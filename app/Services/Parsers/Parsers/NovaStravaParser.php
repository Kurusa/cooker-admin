<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMXPath;

class NovaStravaParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $node = $this->xpath->query("//div[contains(@class, 'wprm-recipe') and contains(@class, 'wprm-recipe-template-classic')]")?->item(0);

        if (!$node) {
            $node = $this->xpath->query("//div[contains(@class, 'post_content') and contains(@class, 'post_content_single') and contains(@class, 'entry-content')]")?->item(0);
        }

        return $node;
    }

    public function isExcludedByCategory(string $url): bool
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $breadcrumbs = $xpath->query("//div[contains(@class, 'breadcrumbs')]//a");

        foreach ($breadcrumbs as $breadcrumb) {
            $text = mb_strtolower(trim($breadcrumb->nodeValue));
            if (str_contains($text, 'статті')) {
                return true;
            }
        }

        return false;
    }

    public function getSourceKey(): string
    {
        return 'novastrava';
    }
}
