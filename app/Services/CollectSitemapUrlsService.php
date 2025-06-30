<?php

namespace App\Services;

use App\Jobs\SourceSitemap\CheckIfRecipeUrlIsExcludedJob;
use App\Models\Source\Source;
use App\Models\Source\SourceRecipeUrl;
use App\Models\Source\SourceSitemap;
use Exception;
use SimpleXMLElement;

class CollectSitemapUrlsService
{
    public function collectSitemapUrls(Source $source): void
    {
        /** @var SourceSitemap $sitemap */
        foreach ($source->sitemaps as $sitemap) {
            $this->crawlSitemap($source, $sitemap->url);
        }
    }

    private function crawlSitemap(
        Source $source,
        string $sitemapUrl
    ): void
    {
        try {
            $sitemapElements = simplexml_load_file($sitemapUrl);
        } catch (Exception) {
            $sitemapElements = $this->loadSitemapWithCurl($sitemapUrl);
        }

        $urls = $sitemapElements->url ?? $sitemapElements;

        foreach ($urls as $sitemapElement) {
            $sitemapUrl = (string)$sitemapElement->loc;

            if ($this->isSitemap($sitemapUrl)) {
                $this->crawlSitemap($source, $sitemapUrl);
            } else {
                /** @var SourceRecipeUrl $sourceRecipeUrl */
                $sourceRecipeUrl = SourceRecipeUrl::updateOrCreate([
                    'url' => $sitemapUrl,
                    'source_id' => $source->id,
                ]);

                CheckIfRecipeUrlIsExcludedJob::dispatchSync(
                    $sourceRecipeUrl,
                    $source->id,
                );
            }
        }
    }

    private function loadSitemapWithCurl(string $url): ?SimpleXMLElement
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; RecipeBot/1.0)',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            return null;
        }

        return simplexml_load_string($response) ?: null;
    }

    private function isSitemap(string $url): bool
    {
        return str_ends_with($url, '.xml');
    }
}
