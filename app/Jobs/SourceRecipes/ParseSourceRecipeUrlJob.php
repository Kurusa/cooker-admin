<?php

namespace App\Jobs\SourceRecipes;

use App\Models\Source\SourceRecipeUrl;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\ProcessRecipeUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseSourceRecipeUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public readonly SourceRecipeUrl $sourceRecipeUrl,
    )
    {
        $this->onQueue('parsing');
    }

    public function handle(
        RecipeParserFactory     $parserFactory,
        ProcessRecipeUrlService $service,
    ): void
    {
        $parser = $parserFactory->make($this->sourceRecipeUrl->source->title);
        $service->processRecipeUrl($this->sourceRecipeUrl, $parser);
    }
}
