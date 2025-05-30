<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMNode;
use DOMXPath;

/**
 * Removes global unwanted nodes from the DOM. The entire tag and its contents are removed.
 */
class RemoveGlobalJunkNodesStep implements CleanerStepInterface
{
    protected array $globalXpaths = [
        './/style',
        './/script',
        './/svg',
    ];

    public function handle(DOMNode $node): void
    {
        $xpath = new DOMXPath($node->ownerDocument);

        foreach ($this->globalXpaths as $expression) {
            $nodes = $xpath->query($expression, $node);

            foreach (iterator_to_array($nodes) as $junkNode) {
                $junkNode->parentNode?->removeChild($junkNode);
            }
        }
    }
}
