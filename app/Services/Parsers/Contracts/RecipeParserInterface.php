<?php

namespace App\Services\Parsers\Contracts;

use App\DTO\RecipeDTO;
use DOMNode;

interface RecipeParserInterface
{
    public function extractRecipeNode(): DOMNode;

    public function isExcludedByCategory(string $url): bool;

    public function getSourceKey(): string;
}
