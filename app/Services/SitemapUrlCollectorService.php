<?php

namespace App\Services;

use App\Jobs\SourceSitemap\CheckIfRecipeUrlIsExcludedJob;
use App\Models\Source\Source;
use App\Models\Source\SourceRecipeUrl;
use App\Models\Source\SourceSitemap;

class SitemapUrlCollectorService
{
    public function __construct(
        private readonly Source $source,
    )
    {
    }

    /**
     * @return array<SourceRecipeUrl>
     */
    public function getFilteredSitemapUrls(): array
    {
        $urls = [];

        /** @var SourceSitemap $sitemap */
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

            if ($this->isSitemap($url)) {
                $this->parseSitemapUrls($url, $urls);
            } else {
                /** @var SourceRecipeUrl $sourceRecipeUrl */
                $sourceRecipeUrl = SourceRecipeUrl::updateOrCreate([
                    'url' => $url,
                    'source_id' => $this->source->id,
                ]);

                CheckIfRecipeUrlIsExcludedJob::dispatch(
                    $sourceRecipeUrl,
                    $this->source->id,
                );
            }
        }
    }

    private function isSitemap(string $url): bool
    {
        return str_ends_with($url, '.xml');
    }
}
