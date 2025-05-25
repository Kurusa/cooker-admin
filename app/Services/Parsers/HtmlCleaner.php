<?php

namespace App\Services\Parsers;

use App\Services\Parsers\Contracts\CleanerStepInterface;
use App\Services\Parsers\Contracts\HtmlCleanerInterface;
use DOMNode;

class HtmlCleaner implements HtmlCleanerInterface
{
    public function __construct(
        protected iterable $steps,
    )
    {
    }

    public function cleanup(DOMNode $node): string
    {
        /** @var CleanerStepInterface $step */
        foreach ($this->steps as $step) {
            $step->handle($node);
        }

        $html = str_replace(["\n", "\r", "\t"], '', $node->ownerDocument->saveHTML($node));

        $html = preg_replace('/>\s+</', '><', $html);
        $html = preg_replace('/\s{2,}/', ' ', $html);
        $html = preg_replace('/[\r\n\t]+/', '', $html);

        return trim($html);
    }
}
