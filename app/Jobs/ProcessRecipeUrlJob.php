<?php

namespace App\Jobs;

use App\Models\Source\SourceRecipeUrl;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\ProcessRecipeUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRecipeUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly RecipeParserInterface $parser,
        public readonly SourceRecipeUrl       $sourceRecipeUrl,
    )
    {
    }

    public function handle(
        ProcessRecipeUrlService $service,
    ): void
    {
        $service->processRecipeUrl($this->sourceRecipeUrl, $this->parser);
    }
}
