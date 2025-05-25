<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMNode;

class RemoveCommentsStep implements CleanerStepInterface
{
    public function handle(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
            } else {
                $this->handle($child);
            }
        }
    }
}
