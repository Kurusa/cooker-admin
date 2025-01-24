<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use DOMXPath;

class NovaStravaParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//h1[@class='sc_layouts_title_caption']");
    }

    public function parseCategories(DOMXPath $xpath): array
    {
        return $this->extractCleanSingleValue($xpath, "//a[@class='breadcrumbs_item cat_post']");
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        $totalCookingTime = 0;

        $rawHours = $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-total_time-hours')]/text()");
        $rawMinutes = $this->extractCleanSingleValue($xpath, "//span[contains(@class, 'wprm-recipe-total_time-minutes')]/text()");

        if ($rawHours) {
            $totalCookingTime += (int) $rawHours * 60;
        }

        if ($rawMinutes) {
            $totalCookingTime += (int) $rawMinutes;
        }

        return $totalCookingTime;
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        $class = 'wprm-recipe-servings wprm-recipe-details wprm-block-text-normal';

        $portions = (int) $this->extractCleanSingleValue($xpath, "//span[@class='$class']");

        return $portions > 0 ? $portions : 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = $this->extractMultipleValues($xpath, "//ul[@class='wprm-recipe-ingredients']/li");

        return $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        return $this->extractMultipleValues($xpath, "//ul[@class='wprm-recipe-instructions']/li");
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query("//div[@class='wprm-recipe-image wprm-block-image-normal']/img")->item(0);

        if ($src = $imageNode?->getAttribute('src')) {
            return str_replace('150', '370', $src);
        }

        return '';
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            'all-posts',
            '-yak-',
            '/yak-',
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
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
