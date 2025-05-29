<?php

namespace App\Services\Parsers\Contracts;

use DOMNode;

interface RecipeParserInterface
{
    public function extractRecipeNode(): DOMNode;

    public function isExcludedByCategory(string $url): bool;
}
