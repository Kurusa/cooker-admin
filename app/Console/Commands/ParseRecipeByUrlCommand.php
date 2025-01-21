<?php

namespace App\Console\Commands;

use App\Models\SourceRecipeUrl;
use App\Services\FindSourceByTitle;
use App\Services\Parsers\ProcessRecipeUrlService;
use App\Services\Parsers\RecipeParserFactory;
use Illuminate\Console\Command;

class ParseRecipeByUrlCommand extends Command
{
    protected $signature = 'parse:recipe:url {source} {url}';

    protected $description = 'Parse a single recipe by its URL';

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

        /** @var SourceRecipeUrl $sourceRecipeUrl */
        $sourceRecipeUrl = $source->recipeUrls()->updateOrCreate(['url' => $this->argument('url')], [
            'url' => $this->argument('url'),
        ]);

        $parser = $this->parserFactory->make($source->title);
        $this->processRecipeUrlService->processRecipeUrl($sourceRecipeUrl, $parser);
    }
}
