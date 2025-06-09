<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMElement;
use DOMNode;

/**
 * Removes specific HTML tags from the DOM while preserving their inner content.
 *
 * This step unwraps tags like <br>, <strong>, <ins>, and <figure> by:
 * - Moving their children into the parent node in place of the tag
 * - Removing the tag itself
 *
 * Only the tags are removed; their textual and nested content is preserved.
 */
class RemoveSpecificTagsStep implements CleanerStepInterface
{
    protected array $tagsToRemove = [
        'br',
        'strong',
        'ins',
        'figure',
        'article',
        'span',
        'ol',
        'i',
        'li',
        'h3',
        'h1',
    ];

    public function handle(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $this->handle($child);

                if (in_array(strtolower($child->tagName), $this->tagsToRemove, true)) {
                    while ($child->firstChild) {
                        $child->parentNode->insertBefore($child->firstChild, $child);
                    }
                    $child->parentNode?->removeChild($child);
                }
            }
        }
    }
}
