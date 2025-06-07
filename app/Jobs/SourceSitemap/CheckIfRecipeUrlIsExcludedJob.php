<?php

namespace App\Jobs\SourceSitemap;

use App\Models\Source\SourceRecipeUrl;
use App\Services\Parsers\RecipeParserFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckIfRecipeUrlIsExcludedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly SourceRecipeUrl $sourceRecipeUrl,
    )
    {
        $this->onQueue('excluded_check');
    }

    public function handle(
        RecipeParserFactory $parserFactory,
    ): void
    {
        $parser = $parserFactory->make($this->sourceRecipeUrl->source->title);
        $isExcluded = $parser->isExcluded($this->sourceRecipeUrl);

        if (
            $isExcluded
            && !$this->sourceRecipeUrl->recipes()->count()
            && !$this->sourceRecipeUrl->is_excluded
        ) {
            $this->sourceRecipeUrl->exclude();
        }
    }
}
