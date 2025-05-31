<?php

namespace App\Services\Parsers;

use App\Services\AiProviders\DeepseekService;

class TelegramParserService
{
    public function __construct(
        protected DeepseekService $deepseekService,
    )
    {
    }

    public function parseText(string $text): array
    {
        return $this->deepseekService->parse($text);
    }
}
