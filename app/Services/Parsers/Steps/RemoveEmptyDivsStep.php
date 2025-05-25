<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMElement;
use DOMNode;

class RemoveEmptyDivsStep implements CleanerStepInterface
{
    public function handle(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $this->handle($child);
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
