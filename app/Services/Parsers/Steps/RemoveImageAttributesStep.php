<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMAttr;
use DOMElement;
use DOMNode;

/**
 * Removes unwanted attributes from all <img> elements in the DOM.
 *
 * Specifically removes attributes like "decoding", "width", "height", "alt", "loading", "srcset", "sizes", "rel", "align", "title",
 * as well as any "onload*" attributes.
 *
 * The <img> tags and their main "src" attribute are preserved — only secondary or behavioral attributes are stripped.
 */
class RemoveImageAttributesStep implements CleanerStepInterface
{
    protected array $attributesToRemove = [
        'decoding',
        'width',
        'height',
        'alt',
        'loading',
        'srcset',
        'sizes',
        'rel',
        'align',
        'title',
    ];

    public function handle(DOMNode $node): void
    {
        foreach ($node->getElementsByTagName('img') as $image) {
            $this->cleanImageAttributes($image);
        }
    }

    protected function cleanImageAttributes(DOMElement $image): void
    {
        foreach (iterator_to_array($image->attributes) as $attribute) {
            if (
                in_array($attribute->nodeName, $this->attributesToRemove, true) ||
                str_starts_with($attribute->nodeName, 'onload')
            ) {
                $image->removeAttribute($attribute->nodeName);
            }
        }
    }
}
