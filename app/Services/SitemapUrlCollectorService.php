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

            $passRuleValidation = $this->parser->urlRule($url);

            if ($this->isSitemap($url)) {
                echo "Processing xml url: $url" . PHP_EOL;
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

                if ($passRuleValidation) {
                    if (!$sourceRecipeUrl->recipe()->exists()) {
                        $urls[] = $sourceRecipeUrl;
                    }

                    if ($sourceRecipeUrl->is_excluded && $sourceRecipeUrl->recipe()->exists()) {
                        $sourceRecipeUrl->update([
                            'is_excluded' => false,
                        ]);
                    }
                } else {
                    $sourceRecipeUrl->update([
                        'is_excluded' => true,
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
