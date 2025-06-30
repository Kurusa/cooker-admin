<?php

namespace App\Enums;

enum AiProviderEnum: string
{
    case DEEPSEEK = 'deepseek';
    case GEMINI = 'gemini';
    case OPENAI = 'openai';
}
