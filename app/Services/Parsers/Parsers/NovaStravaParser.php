<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;

class NovaStravaParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $node = $this->xpath->query("//div[contains(@class, 'wprm-recipe') and contains(@class, 'wprm-recipe-template-classic')]")?->item(0);

        if (!$node) {
            $node = $this->xpath->query("//div[contains(@class, 'post_content') and contains(@class, 'post_content_single') and contains(@class, 'entry-content')]")?->item(0);
        }

        return $node;
    }

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

        $disallowedUrls = [
            'https://novastrava.com/tri-sposobi-yak-vyaliti-pomidori/',
            'https://novastrava.com/marinad-dlya-ovochiv-gril/',
        ];

        if (in_array($url, $disallowedUrls, true)) {
            return true;
        }

        return false;
    }

    public function isExcludedByCategory(string $url): bool
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $breadcrumbs = $xpath->query("//div[contains(@class, 'breadcrumbs')]//a");

        foreach ($breadcrumbs as $breadcrumb) {
            $text = mb_strtolower(trim($breadcrumb->nodeValue));
            if (str_contains($text, 'статті')) {
                return true;
            }
        }

        return false;
    }
}
