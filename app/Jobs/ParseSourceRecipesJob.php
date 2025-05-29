<?php

namespace App\Jobs;

use App\Models\Source\Source;
use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseSourceRecipesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        private readonly Source $source,
    )
    {
    }

    public function handle(): void
    {
        $sourceRecipeUrls = $this->source->unparsedRecipes();

        /** @var SourceRecipeUrl $sourceRecipeUrl */
        foreach ($sourceRecipeUrls as $sourceRecipeUrl) {
            ProcessRecipeUrlJob::dispatch($sourceRecipeUrl);
        }
    }
}
