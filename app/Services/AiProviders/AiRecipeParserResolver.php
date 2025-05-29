<?php

namespace App\Services\AiProviders;

use App\Enums\Source\AiProvider;
use App\Services\AiProviders\Contracts\AiRecipeParserServiceInterface;

class AiRecipeParserResolver
{
    public function __construct(
        private readonly DeepseekService $deepseekService,
        private readonly GeminiService   $geminiService,
    )
    {
    }

    public function resolve(AiProvider $provider): AiRecipeParserServiceInterface
    {
        return match ($provider) {
            AiProvider::DEEPSEEK => $this->deepseekService,
            AiProvider::GEMINI => $this->geminiService,
        };
    }
}
