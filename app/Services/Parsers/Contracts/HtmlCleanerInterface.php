<?php

namespace App\Services\Parsers\Contracts;

use DOMNode;

interface HtmlCleanerInterface
{
    public function cleanup(DOMNode $node): string;
}
