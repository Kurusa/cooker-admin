<?php

namespace App\Services\Parsers;

use App\Services\DeepseekService;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

abstract class BaseRecipeParser implements RecipeParserInterface
{
    protected DOMXPath $xpath;

    abstract public function urlRule(string $url): bool;

    public function parseRecipes(string $url): array
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $this->xpath = new DOMXPath($dom);

        $block = $this->extractRecipeBlock();

        if (strlen($block)) {
            /** @var DeepseekService $service */
            $service = app(DeepseekService::class);

            return $service->parseRecipeFromHtml($block);
        }

        return [];
    }

    protected function removeGlobalJunkNodes(DOMNode $context): void
    {
        $globalXpaths = [
            './/style',
            './/script',
            './/svg',
        ];

        foreach ($globalXpaths as $xpath) {
            $nodes = (new DOMXPath($context->ownerDocument))->query($xpath, $context);
            foreach (iterator_to_array($nodes) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }
    }

    protected function cleanImageAttributes(DOMNode $node): void
    {
        foreach ($node->getElementsByTagName('img') as $img) {
            foreach (iterator_to_array($img->attributes) as $attr) {
                if (in_array($attr->nodeName, ['decoding', 'width', 'height', 'alt', 'loading', 'srcset', 'sizes'])
                    || str_starts_with($attr->nodeName, 'data-')
                    || str_starts_with($attr->nodeName, 'onload')
                ) {
                    $img->removeAttribute($attr->nodeName);
                }
            }
        }
    }

    protected function removeAllClassesAndAttributes(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            $node->removeAttribute('class');
            $node->removeAttribute('id');
            $node->removeAttribute('style');
            $node->removeAttribute('aria-hidden');
            $node->removeAttribute('aria-label');

            foreach (iterator_to_array($node->attributes) as $attribute) {
                if (str_starts_with($attribute->nodeName, 'data-')) {
                    $node->removeAttribute($attribute->nodeName);
                }
            }
        }

        foreach ($node->childNodes as $child) {
            $this->removeAllClassesAndAttributes($child);
        }
    }

    protected function removeEmptyDivs(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $this->removeEmptyDivs($child);

                if (
                    $child->tagName === 'div'
                    && !$child->hasChildNodes()
                    && trim($child->textContent) === ''
                ) {
                    $child->parentNode?->removeChild($child);
                }
            }
        }
    }
}
