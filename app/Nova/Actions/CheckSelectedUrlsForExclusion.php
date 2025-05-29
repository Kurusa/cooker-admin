<?php

namespace App\Nova\Actions;

use App\Jobs\SourceSitemap\CheckIfRecipeUrlIsExcludedJob;
use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class CheckSelectedUrlsForExclusion extends Action implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public $name = 'Check for exclusion';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): void
    {
        /** @var SourceRecipeUrl $sourceRecipeUrl */
        foreach ($models as $sourceRecipeUrl) {
            CheckIfRecipeUrlIsExcludedJob::dispatch($sourceRecipeUrl);
        }
    }
}
