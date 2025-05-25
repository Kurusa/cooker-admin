<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMAttr;
use DOMElement;
use DOMNode;

class RemoveClassesAndAttributesStep implements CleanerStepInterface
{
    public function handle(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            $node->removeAttribute('class');
            $node->removeAttribute('id');
            $node->removeAttribute('style');
            $node->removeAttribute('aria-hidden');
            $node->removeAttribute('aria-label');
            $node->removeAttribute('title');
            $node->removeAttribute('itemprop');
            $node->removeAttribute('datetime');

            /** @var DOMAttr $attribute */
            foreach (iterator_to_array($node->attributes) as $attribute) {
                if ($attribute->nodeName == 'data-wpfc-original-src' || $attribute->nodeName == 'data-src') {
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
            $this->handle($child);
        }
    }
}
