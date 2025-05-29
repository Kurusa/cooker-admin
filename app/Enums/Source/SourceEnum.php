<?php

namespace App\Enums\Source;

use App\Services\Parsers\Parsers\AllRecipesParser;
use App\Services\Parsers\Parsers\FayniReceptyParser;
use App\Services\Parsers\Parsers\FoodcourtParser;
use App\Services\Parsers\Parsers\JistyParser;
use App\Services\Parsers\Parsers\MonchefParser;
use App\Services\Parsers\Parsers\NovaStravaParser;
use App\Services\Parsers\Parsers\PatelnyaParser;
use App\Services\Parsers\Parsers\PicanteParser;
use App\Services\Parsers\Parsers\RetseptyParser;
use App\Services\Parsers\Parsers\RudParser;
use App\Services\Parsers\Parsers\SmachnoParser;
use App\Services\Parsers\Parsers\TandiParser;
use App\Services\Parsers\Parsers\VseReceptyParser;
use App\Services\Parsers\Parsers\YabpoelaParser;

enum SourceEnum: string
{
    case PATELNYA = 'patelnya';
    case FAYNI = 'fayni';
    case NOVASTRAVA = 'novastrava';
    case RUD = 'rud';
    case JISTY = 'jisty';
    case FOODCOURT = 'foodcourt';
    case PICANTE = 'picante';
    case VSERECEPTY = 'vse-recepty';
    case ALLRECIPES = 'allrecipes';
    case SMACHNO = 'smachno';
    case YABPOELA = 'yabpoela';
    case MONCHEF = 'monchef';
    case TANDI = 'tandi';
    case RETSEPTYUA = 'retsepty-ua';

    // case ETNOCOOK = 'etnocook'; китайсько-англійське
    // case COOKPAD = 'cookpad'; нема sitemap
    // case RETSEPTYONLINE = 'retsepty-online'; https://retsepty.online.ua русняве
    // case RETSEPT = 'retsepty'; https://retsept.net русняве
    // case cookpad = 'cookpad'; нема sitemap
//            $factory->registerParser('tsn', TsnParser::class);
//            $factory->registerParser('ua-recept', UaReceptParser::class);
    public function parserClass(): string
    {
        return match ($this) {
            self::PATELNYA => PatelnyaParser::class,
            self::FAYNI => FayniReceptyParser::class,
            self::NOVASTRAVA => NovaStravaParser::class,
            self::RUD => RudParser::class,
            self::JISTY => JistyParser::class,
            self::FOODCOURT => FoodcourtParser::class,
            self::PICANTE => PicanteParser::class,
            self::VSERECEPTY => VseReceptyParser::class,
            self::ALLRECIPES => AllRecipesParser::class,
            self::SMACHNO => SmachnoParser::class,
            self::YABPOELA => YabpoelaParser::class,
            self::MONCHEF => MonchefParser::class,
            self::TANDI => TandiParser::class,
            self::RETSEPTYUA => RetseptyParser::class,
        };
    }
}
