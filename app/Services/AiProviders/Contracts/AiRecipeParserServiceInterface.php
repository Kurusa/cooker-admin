<?php

namespace App\Services\AiProviders\Contracts;

use Illuminate\Support\Collection;

interface AiRecipeParserServiceInterface
{
    public function parse(string $html): array;

    public function categorizeRecipes(Collection $recipes): array;
}
