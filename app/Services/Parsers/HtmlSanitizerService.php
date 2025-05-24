<?php

namespace App\Services\Parsers;

use DOMDocument;
use DOMXPath;

class HtmlSanitizerService
{
    public function sanitizeHtml(string $html): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);

        $this->removeNodes($xpath, [
            '//script',
            '//style',
            '//svg',
            '//footer',
            '//div[contains(@class, "social")]',
            '//div[contains(@class, "ast-post-social-sharing")]',
            '//div[contains(@class, "wprm-recipe-pin")]',
            '//div[contains(@class, "wprm-recipe-print")]',
            '//div[contains(@class, "wprm-recipe-jump-to-comments")]',
            '//div[contains(@class, "breadcrumbs")]',
            '//a[contains(@href, "pinterest.com")]',
            '//a[contains(@href, "facebook.com")]',
        ]);

        return $dom->saveHTML();
    }

    private function removeNodes(DOMXPath $xpath, array $queries): void
    {
        foreach ($queries as $query) {
            foreach ($xpath->query($query) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }
    }
}
