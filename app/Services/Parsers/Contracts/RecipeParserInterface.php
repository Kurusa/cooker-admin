<?php

namespace App\Services\Parsers\Contracts;

use App\DTO\RecipeDTO;
use DOMNode;

interface RecipeParserInterface
{
    /** @return RecipeDTO[] */
    public function parseRecipes(string $url): array;

    public function extractRecipeNode(): DOMNode;

    public function isExcludedByCategory(string $url): bool;

    public function isExcludedByUrlRule(string $url): bool;

    public function getSourceKey(): string;
}
