<?php

namespace App\Services\Parsers\Contracts;

use DOMNode;

interface CleanerStepInterface
{
    public function handle(DOMNode $node): void;
}
