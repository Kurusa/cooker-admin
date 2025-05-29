<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMXPath;

class TandiParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        return $this->xpath->query("//div[contains(@class, 'td-post-content tagdiv-type')]")->item(0);
    }

    public function isExcludedByCategory(string $url): bool
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $nodes = $xpath->query("//ul[contains(@class, 'td-category')]//li[contains(@class, 'entry-category')]//a");

        if ($nodes->length === 1) {
            $text = mb_strtolower(trim($nodes->item(0)->nodeValue));
            return $text === 'блог';
        }

        return false;
    }
}
