<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMElement;
use DOMNode;

/**
 * Removes all event handler attributes (e.g., onclick, onload, onmouseover) from all elements.
 */
class RemoveJsEventAttributesStep implements CleanerStepInterface
{
    public function handle(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            foreach (iterator_to_array($node->attributes) as $attribute) {
                if (str_starts_with(strtolower($attribute->nodeName), 'on')) {
                    $node->removeAttribute($attribute->nodeName);
                }
            }
        }

        foreach ($node->childNodes as $child) {
            $this->handle($child);
        }
    }
}
