<?php

namespace App\Services\Parsers\Contracts;

use App\Enums\Recipe\Complexity;
use App\Models\Source;
use DOMXPath;

interface RecipeParserInterface
{
    public function parseTitle(DOMXPath $xpath): string;

    public function parseImage(DOMXPath $xpath): ?string;

    public function parseCategory(DOMXPath $xpath): string;

    public function parseComplexity(DOMXPath $xpath): Complexity;

    public function parseCookingTime(DOMXPath $xpath): ?int;

    public function parsePortions(DOMXPath $xpath): ?int;

    public function parseIngredients(DOMXPath $xpath): array;

    public function parseSteps(DOMXPath $xpath): array;
}
