<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMNode;
use DOMXPath;

class RemoveGlobalJunkNodesStep implements CleanerStepInterface
{
    public function handle(DOMNode $node): void
    {
        $globalXpaths = [
            './/style',
            './/script',
            './/svg',
        ];

        foreach ($globalXpaths as $xpath) {
            $nodes = (new DOMXPath($node->ownerDocument))->query($xpath, $node);

            foreach (iterator_to_array($nodes) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }
    }
}
