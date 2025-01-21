<?php

namespace App\Services\Parsers;

use App\Exceptions\UnknownSourceException;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\Parsers\Parsers\FayniReceptyParser;
use App\Services\Parsers\Parsers\JistyParser;
use App\Services\Parsers\Parsers\NovaStravaParser;
use App\Services\Parsers\Parsers\PatelnyaParser;
use App\Services\Parsers\Parsers\PicanteParser;
use App\Services\Parsers\Parsers\RetseptyParser;
use App\Services\Parsers\Parsers\RudParser;
use App\Services\Parsers\Parsers\SmachnoParser;
use App\Services\Parsers\Parsers\TandiParser;
use App\Services\Parsers\Parsers\TsnParser;
use App\Services\Parsers\Parsers\UaReceptParser;
use App\Services\Parsers\Parsers\VseReceptyParser;
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
            'rud' => new RudParser(),
            'tsn' => new TsnParser(),
            'smachno' => new SmachnoParser(),
            'picante' => new PicanteParser(),
            'retsepty' => new RetseptyParser(),
            'vse-recepty' => new VseReceptyParser(),
            'ua-recept' => new UaReceptParser(),
            'tandi' => new TandiParser(),
            default => throw new UnknownSourceException(),
        };
    }
}
