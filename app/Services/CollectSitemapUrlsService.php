<?php

namespace App\Services;

use App\Jobs\SourceSitemap\CheckIfRecipeUrlIsExcludedJob;
use App\Models\Source\Source;
use App\Models\Source\SourceRecipeUrl;
use App\Models\Source\SourceSitemap;

class CollectSitemapUrlsService
{
    public function __construct(
        private readonly Source $source,
    )
    {
    }

    public function collectSitemapUrls(): void
    {
        /** @var SourceSitemap $sitemap */
        foreach ($this->source->sitemaps as $sitemap) {
            $this->crawlSitemap($sitemap->url);
        }
    }

    private function crawlSitemap(string $sitemapUrl): void
    {
        $sitemapElements = simplexml_load_file($sitemapUrl);

        foreach ($sitemapElements as $sitemapElement) {
            $sitemapUrl = (string)$sitemapElement->loc;

            if ($this->isSitemap($sitemapUrl)) {
                $this->crawlSitemap($sitemapUrl);
            } else {
                /** @var SourceRecipeUrl $sourceRecipeUrl */
                $sourceRecipeUrl = SourceRecipeUrl::updateOrCreate([
                    'url' => $sitemapUrl,
                    'source_id' => $this->source->id,
                ]);

                CheckIfRecipeUrlIsExcludedJob::dispatchSync(
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
