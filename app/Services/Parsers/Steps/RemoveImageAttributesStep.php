<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMAttr;
use DOMElement;
use DOMNode;

class RemoveImageAttributesStep implements CleanerStepInterface
{
    public function handle(DOMNode $node): void
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
}
