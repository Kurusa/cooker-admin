<?php

namespace App\Console\Commands;

use App\Models\Source\Source;
use App\Services\CollectSitemapUrlsService;
use Illuminate\Console\Command;

class CollectSourceSitemapUrlsCommand extends Command
{
    protected $signature = 'collect:source:sitemap-urls {sourceId}';

    protected $description = 'Collect source sitemap urls';

    public function handle(): void
    {
        /** @var Source $source */
        $source = Source::find($this->argument('sourceId'));

        $service = new CollectSitemapUrlsService($source);
        $service->collectSitemapUrls();
    }
}
