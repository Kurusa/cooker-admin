<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMElement;
use DOMNode;

/**
 * Removes standard presentational and semantic attributes such as "class", "style", "id", etc.
 * Removes all custom "data-" attributes except for a small allowlist (e.g., "data-src", "data-wpfc-original-src").
 * Removes any attribute whose value contains the word "gif"
 */
class RemoveClassesAndAttributesStep implements CleanerStepInterface
{
    protected array $standardAttributesToRemove = [
        'class',
        'id',
        'style',
        'aria-hidden',
        'aria-label',
        'title',
        'itemprop',
        'itemscope',
        'itemtype',
        'datetime',
        'target',
        'fetchpriority',
        'start',
        'rel',
    ];

    protected array $preservedDataAttributes = [
        'data-wpfc-original-src',
        'data-src',
    ];

    public function handle(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            $this->removeStandardAttributes($node);
            $this->removeUnwantedCustomAttributes($node);
        }

        foreach ($node->childNodes as $child) {
            $this->handle($child);
        }
    }

    protected function removeStandardAttributes(DOMElement $node): void
    {
        foreach ($this->standardAttributesToRemove as $attr) {
            $node->removeAttribute($attr);
        }
    }

    protected function removeUnwantedCustomAttributes(DOMElement $node): void
    {
        foreach (iterator_to_array($node->attributes) as $attribute) {
            if (in_array($attribute->nodeName, $this->preservedDataAttributes, true)) {
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
}
