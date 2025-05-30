<?php

namespace App\Services\Parsers\Steps;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use DOMElement;
use DOMNode;

class RemoveSvgAttributesStep implements CleanerStepInterface
{
    protected array $attributesToCheck = [
        'src',
        'href',
        'data-src',
        'data-href',
    ];

    public function handle(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            foreach ($this->attributesToCheck as $attr) {
                $value = $node->getAttribute($attr);

                if ($value && $this->isSvg($value)) {
                    $node->removeAttribute($attr);
                }
            }
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            $this->handle($child);
        }
    }

    protected function isSvg(string $value): bool
    {
        return str_contains($value, 'data:image/svg') || str_ends_with(strtolower($value), '.svg');
    }
}
