<?php

namespace App\Services\Parsers\Contracts;

use App\DTO\IngredientDTO;
use App\DTO\RecipeDTO;
use App\DTO\StepDTO;
use App\Enums\Recipe\Complexity;

interface RecipeParserInterface
{
    /** @return RecipeDTO[] */
    public function parseRecipes(string $url): array;

    public function extractRecipeBlock(): string;
}
