<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMElement;
use DOMNode;

/**
 * Removes all elements that are completely empty (no child nodes or text).
 */
class RemoveEmptyElementsStep implements CleanerStepInterface
{
    public function handle(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $this->handle($child);

                if (!$child->hasChildNodes() && trim($child->textContent) === '') {
                    $child->parentNode?->removeChild($child);
                }
            }
        }
    }
}
