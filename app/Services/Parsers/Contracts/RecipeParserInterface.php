<?php

namespace App\Services\Parsers\Contracts;

use App\DTO\RecipeDTO;
use App\Enums\Recipe\Complexity;
use DOMXPath;

interface RecipeParserInterface
{
    /**
     * @return RecipeDTO[]
     */
    public function parseRecipes(DOMXPath $xpath): array;

    public function parseTitle(DOMXPath $xpath): string;

    public function parseImage(DOMXPath $xpath): string;

    public function parseCategories(DOMXPath $xpath): array;

    public function parseComplexity(DOMXPath $xpath): Complexity;

    public function parseCookingTime(DOMXPath $xpath): ?int;

    public function parsePortions(DOMXPath $xpath): int;

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array;

    public function parseSteps(DOMXPath $xpath): array;
}
