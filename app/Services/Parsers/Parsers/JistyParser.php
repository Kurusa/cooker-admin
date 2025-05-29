<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMXPath;

class JistyParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $headerNode = $this->xpath->query("//main[@class='position-relative']//div[@class='container']/div[@class='text-center']")?->item(0);
        $contentNode = $this->xpath->query("//main[@class='position-relative']//div[@class='container']//div[@class='col-lg-8 col-md-12']")?->item(0);

        $wrapper = $this->xpath->document->createElement('div');
        $wrapper->setAttribute('class', 'recipe-wrapper');

        if ($headerNode) {
            $wrapper->appendChild($headerNode->cloneNode(true));
        }

        if ($contentNode) {
            $wrapper->appendChild($contentNode->cloneNode(true));
        }

        $comments = $this->xpath->query(".//div[@id='comments']", $wrapper);
        foreach (iterator_to_array($comments) as $comment) {
            $comment->parentNode?->removeChild($comment);
        }

        return $wrapper;
    }

    public function isExcludedByCategory(string $url): bool
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $excludedCategories = [
            'вінегрет',
            'техніка для кухні',
            'продукти',
            'ресторани',
            'гроші',
        ];

        $categoryValue = $xpath->query("//span[@class='post-cat bg-warning']")->item(0)?->nodeValue;

        if (!$categoryValue) {
            return false;
        }

        foreach ($excludedCategories as $excludedCategory) {
            if (str_contains(mb_strtolower($categoryValue), $excludedCategory)) {
                return true;
            }
        }

        return false;
    }
}
