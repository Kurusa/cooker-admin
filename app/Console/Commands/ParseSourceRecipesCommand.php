<?php

namespace App\Console\Commands;

use App\Services\FindSourceByTitle;
use App\Services\Parsers\ProcessRecipeUrlService;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\SitemapUrlCollectorService;
use Illuminate\Console\Command;

class ParseSourceRecipesCommand extends Command
{
    protected $signature = 'parse:recipe:source {source}';

    protected $description = 'Parse a all recipes by source title';

    public function __construct(
        private readonly RecipeParserFactory $parserFactory,
        private readonly ProcessRecipeUrlService $processRecipeUrlService,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $source = FindSourceByTitle::find($this->argument('source'));
        $parser = $this->parserFactory->make($source->title);

        $service = new SitemapUrlCollectorService($parser, $source);
        $sourceRecipeUrls = $service->getFilteredSitemapUrls();

        $progressBar = $this->output->createProgressBar(count($sourceRecipeUrls));
        $progressBar->start();

        foreach ($sourceRecipeUrls as $sourceRecipeUrl) {
            $this->info(PHP_EOL . "Processing: {$sourceRecipeUrl->url}");

            $this->processRecipeUrlService->processRecipeUrl($sourceRecipeUrl, $parser);

            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
