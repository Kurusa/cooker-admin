<?php

namespace App\Enums\Source;

enum AiProvider: string
{
    case DEEPSEEK = 'deepseek';
    case GEMINI = 'gemini';
    case OPENAI = 'openai';
}
