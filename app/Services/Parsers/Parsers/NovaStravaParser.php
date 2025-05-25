<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class NovaStravaParser extends BaseRecipeParser
{
    public function isExcludedByUrlRule(string $url): bool
    {
        $disallowedPatterns = [
            'all-posts',
            'yaki-',
            'yaku-',
            'yakij-',
            'https://novastrava.com/riznovidi-chervonoyi-ribi-ta-yiyi-korist/',
            'https://novastrava.com/najkrashi-garniri-do-kurki/',
            'https://novastrava.com/garniri-do-kachki/',
            'https://novastrava.com/korisni-perekusi-dlya-ditej/',
            'https://novastrava.com/stravi-na-rizdvo/',
            'https://novastrava.com/zmina-koloru-pidsvitki-klaviaturi/',
            'https://novastrava.com/sho-take-ikra/',
            'https://novastrava.com/pansionati-dlya-litnih-lyudej-porivnyannya-cin-ta-poslug/',
            'https://novastrava.com/stilni-ta-zruchni-svitli-kukhni/',
            'https://novastrava.com/nabori-dlya-viski-bohemia/',
            'https://novastrava.com/sho-mozna-isti-pisla-otruenna/',
            'https://novastrava.com/10-faktiv-pro-korist-shashliku/',
            'https://novastrava.com/najpopulyarnishi-garniri-do-ribi/',
            'https://novastrava.com/budinok-dlya-litnikh-lyudei-u-kiyevi/',
            'https://novastrava.com/shtambovi-troyandi/',
            'https://novastrava.com/chim-vidriznyayetsya-vugilnii-gril-vid-mangala/',
            'tovari-',
            'https://novastrava.com/m4-macbook-air-dopomagaye-apple-zalishatsya-na-plavu/',
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public function extractRecipeNode(): DOMNode
    {
        return $this->xpath->query("//div[contains(@class, 'wprm-recipe wprm-recipe-template-classic')]")->item(0);
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
