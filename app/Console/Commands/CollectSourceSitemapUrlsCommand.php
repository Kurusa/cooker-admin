<?php

namespace App\Console\Commands;

use App\Exceptions\UnknownSourceException;
use App\Models\Source\Source;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\SitemapUrlCollectorService;
use Illuminate\Console\Command;

class CollectSourceSitemapUrlsCommand extends Command
{
    protected $signature = 'collect:source:sitemap-urls {sourceId}';

    protected $description = 'Collect source sitemap urls';

    public function __construct(
        private readonly RecipeParserFactory $parserFactory
    )
    {
        parent::__construct();
    }

    /**
     * @throws UnknownSourceException
     */
    public function handle(): void
    {
        /** @var Source $source */
        $source = Source::find($this->argument('sourceId'));

        $parser = $this->parserFactory->make($source->title);

        $service = new SitemapUrlCollectorService($parser, $source);
        $service->getFilteredSitemapUrls();
    }
}
