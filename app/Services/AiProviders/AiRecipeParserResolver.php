<?php

namespace App\Services\AiProviders;

use App\Enums\AiProviderEnum;
use App\Services\AiProviders\Contracts\AiRecipeParserServiceInterface;

class AiRecipeParserResolver
{
    public function __construct(
        private readonly DeepseekService $deepseekService,
        private readonly GeminiService   $geminiService,
        private readonly OpenAiService   $openAiService,
    )
    {
    }

    public function resolve(AiProviderEnum $provider): AiRecipeParserServiceInterface
    {
        return match ($provider) {
            AiProviderEnum::DEEPSEEK => $this->deepseekService,
            AiProviderEnum::GEMINI => $this->geminiService,
            AiProviderEnum::OPENAI => $this->openAiService,
        };
    }
}
