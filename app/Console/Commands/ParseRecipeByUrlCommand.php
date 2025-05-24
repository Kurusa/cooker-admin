<?php

namespace App\Console\Commands;

use App\Jobs\ProcessRecipeUrlJob;
use App\Models\Source\SourceRecipeUrl;
use App\Services\Parsers\RecipeParserFactory;
use Illuminate\Console\Command;

class ParseRecipeByUrlCommand extends Command
{
    protected $signature = 'parse:recipe:url {url}';

    protected $description = 'Parse a single recipe by its URL';

    public function __construct(
        private readonly RecipeParserFactory $parserFactory,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        /** @var SourceRecipeUrl $sourceRecipeUrl */
        $sourceRecipeUrl = SourceRecipeUrl::where('url', $this->argument('url'))->first();

        $parser = $this->parserFactory->make($sourceRecipeUrl->source->title);

        ProcessRecipeUrlJob::dispatch($parser, $sourceRecipeUrl);
    }
}
