<?php

namespace App\Services\Parsers;

use App\Services\Parsers\Contracts\RecipeParserInterface;
use InvalidArgumentException;

class RecipeParserFactory
{
    public static function make(string $source): RecipeParserInterface
    {
        return match ($source) {
            'patelnya' => new PatelnyaParser(),
            default => throw new InvalidArgumentException("Unknown parser source: {$source}")
        };
    }
}
