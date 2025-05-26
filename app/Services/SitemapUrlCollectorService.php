<?php

namespace App\Services;

use App\Models\Source\Source;
use App\Models\Source\SourceRecipeUrl;
use App\Services\Parsers\Contracts\RecipeParserInterface;

class SitemapUrlCollectorService
{
    public function __construct(
        private readonly RecipeParserInterface $parser,
        private readonly Source                $source,
    )
    {
    }

    /**
     * @return array<SourceRecipeUrl>
     */
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
            $url = (string)$sitemapElement->loc;

            $isExcludedByUrlRule = $this->parser->isExcludedByUrlRule($url);
            $isExcludedByCategoryRule = $this->parser->isExcludedByCategory($url);

            if ($this->isSitemap($url)) {
                $this->parseSitemapUrls($url, $urls);
            } else {
                /** @var SourceRecipeUrl $sourceRecipeUrl */
                $sourceRecipeUrl = SourceRecipeUrl::updateOrCreate([
                    'url' => $url,
                    'source_id' => $this->source->id,
                ], [
                    'url' => $url,
                    'source_id' => $this->source->id,
                ]);

                if ($isExcludedByUrlRule || $isExcludedByCategoryRule) {
                    $sourceRecipeUrl->update([
                        'is_excluded' => true,
                    ]);
                } else {
                    if (!$sourceRecipeUrl->recipes()->exists()) {
                        $urls[] = $sourceRecipeUrl;
                    }

                    if ($sourceRecipeUrl->is_excluded) {
                        $sourceRecipeUrl->update([
                            'is_excluded' => false,
                        ]);
                    }
                }
            }
        }
    }

    private function isSitemap(string $url): bool
    {
        return str_ends_with($url, '.xml');
    }
}
