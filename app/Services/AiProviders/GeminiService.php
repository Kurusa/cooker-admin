<?php

namespace App\Services\AiProviders;

use App\Services\AiProviders\Contracts\AiRecipeParserServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class GeminiService implements AiRecipeParserServiceInterface
{
    public function __construct(private readonly Client $client)
    {
    }

    public function parse(string $html): array
    {
        return [];
    }

    public function categorizeRecipes(Collection $recipes): array
    {
        // TODO: Implement categorizeRecipes() method.
    }
}
