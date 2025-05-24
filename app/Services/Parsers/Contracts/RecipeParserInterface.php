<?php

namespace App\Services\Parsers\Contracts;

use App\DTO\IngredientDTO;
use App\DTO\RecipeDTO;
use App\DTO\StepDTO;
use App\Enums\Recipe\Complexity;

interface RecipeParserInterface
{
    /** @return RecipeDTO[] */
    public function parseRecipes(bool $debug = false): array;

    public function parseTitle(): string;

    public function parseCategories(): array;

    public function parseComplexity(): Complexity;

    public function parseCookingTime(): ?int;

    public function parsePortions(): int;

    public function parseImage(): string;

    /** @return IngredientDTO[] */
    public function parseIngredients(bool $debug = false): array;

    /** @return StepDTO[] */
    public function parseSteps(bool $debug = false): array;
}
