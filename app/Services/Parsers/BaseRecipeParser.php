<?php

namespace App\Services\Parsers;

use App\Services\DeepseekService;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Facades\Log;

abstract class BaseRecipeParser implements RecipeParserInterface
{
    protected DOMXPath $xpath;

    abstract public function isExcludedByUrlRule(string $url): bool;

    public function parseRecipes(string $url): array
    {
        Log::info('Parsing recipe: ' . $url);

        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $this->xpath = new DOMXPath($dom);

        /** @var DeepseekService $service */
        $service = app(DeepseekService::class);

        $block = $this->cleanupRecipeNode($this->extractRecipeNode());

        return $service->parseRecipeFromHtml($block);
    }

    private function cleanupRecipeNode(DomNode $recipeNode): string
    {
        $this->removeComments($recipeNode);
        $this->removeEmptyDivs($recipeNode);
        $this->removeAllClassesAndAttributes($recipeNode);
        $this->cleanImageAttributes($recipeNode);
        $this->removeGlobalJunkNodes($recipeNode);

        $block = str_replace(["\n", "\r", "\t"], '', $recipeNode->ownerDocument->saveHTML($recipeNode));

        return $this->minifyHtml($block);
    }

    protected function minifyHtml(string $html): string
    {
        $html = preg_replace('/>\s+</', '><', $html);
        $html = preg_replace('/\s{2,}/', ' ', $html);
        $html = preg_replace('/[\r\n\t]+/', '', $html);

        return trim($html);
    }

    protected function removeComments(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
            } else {
                $this->removeComments($child);
            }
        }
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
        /** @var DOMElement $image */
        foreach ($node->getElementsByTagName('img') as $image) {
            /** @var DOMAttr $attribute */
            foreach (iterator_to_array($image->attributes) as $attribute) {
                if (in_array($attribute->nodeName, [
                        'decoding', 'width', 'height', 'alt', 'loading', 'srcset', 'sizes', 'rel', 'align', 'title',
                    ])
                    || str_starts_with($attribute->nodeName, 'onload')
                ) {
                    $image->removeAttribute($attribute->nodeName);
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
            $node->removeAttribute('title');

            /** @var DOMAttr $attribute */
            foreach (iterator_to_array($node->attributes) as $attribute) {
                if ($attribute->nodeName == 'data-wpfc-original-src') {
                    continue;
                }

                if (
                    str_starts_with($attribute->nodeName, 'data-') ||
                    str_contains($attribute->value, 'gif')
                ) {
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
