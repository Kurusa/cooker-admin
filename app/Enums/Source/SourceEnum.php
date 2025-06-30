<?php

namespace App\Enums\Source;

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
    //case RETSEPTYUA = 'retsepty-ua';
    case FOODNET = 'foodnet';
    case SMAKOTAINFO = 'smakotainfo';
    case COOKERY = 'cookery';

    case LUBYSTOK = 'lubystok';
    case UAPLATFORMA = 'uaplatforma';
    case SHEFKUHAR = 'shefkuhar';
    case OLVENKYLINAR = 'olvenkylinar';
    case EASYCOOKING = 'easycooking';
    case MYROSLAVAPAVLIUCHOK = 'myroslavapavliuchok';
    case GOSPODYNKA = 'gospodynka';
    case VITER = 'viter';
    case GOLDRECIPES = 'goldrecipes';
    case FOODLEZ = 'foodlez';
    case TORCHYN = 'torchyn';

    // case ETNOCOOK = 'etnocook'; китайсько-англійське
    // case COOKORAMA = 'cookorama'; нема sitemap
    // case COOKPAD = 'cookpad'; нема sitemap
    // case RETSEPTYONLINE = 'retsepty-online'; https://retsepty.online.ua русняве
    // case RETSEPT = 'retsepty'; https://retsept.net русняве
    // https://bestrecept.com.ua нема сайтмап
    // https://karri.com.ua нема сайтмап
    // https://rozdil.lviv.ua нема сайтмап

    public function parserClass(): string
    {
        $className = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $this->value)));

        return "App\\Services\\Parsers\\Parsers\\{$className}Parser";
    }
}
