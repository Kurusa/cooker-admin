<?php

namespace App\Console\Commands\Parsers\Patelnya;

use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;

class ParseAttributes extends Command
{
    protected $signature = 'parse:patelnya:recipe-attributes';

    public function handle(): void
    {
        $sitemapUrl = 'https://patelnya.com.ua/post-sitemap2.xml';
        $sitemapElements = simplexml_load_file($sitemapUrl);

        $complexities = [];
        $cookingTimes = [];
        $portions = [];

        foreach ($sitemapElements as $sitemapElement) {
            $url = (string) $sitemapElement->loc;

            $this->info('processing ' . $url);

            $xpath = $this->loadHtml($url);
            $data = $this->extractRecipeData($xpath);

            if ($data['complexity']) {
                $complexities[] = $data['complexity'];
            }

            if ($data['cooking_time']) {
                $cookingTimes[] = $data['cooking_time'];
            }

            if ($data['portions']) {
                $portions[] = $data['portions'];
            }
        }

        $uniqueComplexities = array_unique($complexities);
        $uniqueCookingTimes = array_unique($cookingTimes);
        $uniquePortions = array_unique($portions);

        dd([
            'complexities' => $uniqueComplexities,
            'cooking_times' => $uniqueCookingTimes,
            'portions' => $uniquePortions,
        ]);
    }

    private function loadHtml(string $url): DOMXPath|string
    {
        $html = file_get_contents($url);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);

        return new DOMXPath($dom);
    }

    private function extractRecipeData(DOMXPath $xpath): array
    {
        return [
            'complexity' => $this->extractSingleValue($xpath, ".//div[i/span[contains(text(), 'Рівень складності:')]]/i/span[@class='color-414141']"),
            'cooking_time' => $this->extractSingleValue($xpath, ".//div[i[@class='duration']]/i/span[@class='color-414141 value-title']"),
            'portions' => $this->extractSingleValue($xpath, ".//div[i/span[contains(text(), 'Кількість порцій:')]]/i/span[@class='color-414141 yield']"),
        ];
    }

    private function extractSingleValue(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)->item(0);
        return $node ? str_replace('min', 'хвилин', trim($node->nodeValue)) : null;
    }
}
