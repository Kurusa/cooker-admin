<?php

namespace App\Console\Commands;

use App\Enums\AiProvider;
use App\Jobs\ProcessRecipeUrlJob;
use App\Models\Source\Source;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\SitemapUrlCollectorService;
use Illuminate\Console\Command;

class ParseSourceRecipesCommand extends Command
{
    protected $signature = 'parse:source:recipes {sourceId}';

    protected $description = 'Parse source recipes';

    public function __construct(
        private readonly RecipeParserFactory $parserFactory,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        /** @var Source $source */
        $source = Source::find($this->argument('sourceId'));

        $parser = $this->parserFactory->make($source->title);

        $service = new SitemapUrlCollectorService($parser, $source);
        $sourceRecipeUrls = $service->getFilteredSitemapUrls();

        foreach ($sourceRecipeUrls as $sourceRecipeUrl) {
            ProcessRecipeUrlJob::dispatch(
                $sourceRecipeUrl->id,
                AiProvider::DEEPSEEK,
            );
        }
    }
}
