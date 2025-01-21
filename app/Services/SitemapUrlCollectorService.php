<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Source;
use App\Services\Parsers\Contracts\RecipeParserInterface;

class SitemapUrlCollectorService
{
    public function __construct(
        private readonly RecipeParserInterface $parser,
        private readonly Source $source,
    )
    {
    }

    public function getFilteredSitemapUrls(): array
    {
        $urls = [];

        foreach ($this->source->sitemaps as $sitemap) {
            $this->parseSitemapUrls($sitemap->url, $urls);
        }

        return $urls;
    }

    private function parseSitemapUrls(string $sitemapUrl, array &$urls): void
    {
        $sitemapElements = simplexml_load_file($sitemapUrl);

        foreach ($sitemapElements as $sitemapElement) {
            $url = (string) $sitemapElement->loc;

            if ($this->isSitemap($url)) {
                echo "Processing xml url: $url" . PHP_EOL;
                $this->parseSitemapUrls($url, $urls);
            } elseif ($this->parser->urlRule($url)) {
                if (!Recipe::where('source_url', $url)->exists()) {
                    $urls[] = $url;
                    $this->source->recipeUrls()->updateOrCreate(['url' => $url], ['url' => $url]);
                } else {
                    $this->source->recipeUrls()->updateOrCreate(['url' => $url], [
                        'url' => $url,
                        'is_parsed' => true,
                    ]);
                }
            }
        }
    }

    private function isSitemap(string $url): bool
    {
        return str_ends_with($url, '.xml');
    }
}
