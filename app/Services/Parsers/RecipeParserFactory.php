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
            'fayni' => new FayniReceptyParser(),
            'jisty' => new JistyParser(),
            'novastrava' => new NovaStravaParser(),
            default => throw new InvalidArgumentException("Unknown parser source: {$source}")
        };
    }
}
