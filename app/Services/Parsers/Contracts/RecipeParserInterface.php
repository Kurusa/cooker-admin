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
    public function parseRecipes(): array;

    public function parseTitle(): string;

    public function parseCategories(): array;

    public function parseComplexity(): Complexity;

    public function parseCookingTime(): ?int;

    public function parsePortions(): int;

    public function parseImage(): string;

    public function parseIngredients(bool $debug = false): array;

    public function parseSteps(bool $debug = false): array;
}
