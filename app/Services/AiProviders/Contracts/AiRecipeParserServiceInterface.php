<?php

namespace App\Services\AiProviders\Contracts;

interface AiRecipeParserServiceInterface
{
    public function parse(string $html): array;
}
