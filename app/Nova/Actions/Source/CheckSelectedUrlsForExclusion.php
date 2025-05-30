<?php

namespace App\Nova\Actions\Source;

use App\Jobs\SourceSitemap\CheckIfRecipeUrlIsExcludedJob;
use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;

class CheckSelectedUrlsForExclusion extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Check for exclusion';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var SourceRecipeUrl $sourceRecipeUrl */
        foreach ($models as $sourceRecipeUrl) {

            if ($fields->run_sync) {
                CheckIfRecipeUrlIsExcludedJob::dispatchSync($sourceRecipeUrl);
            } else {
                CheckIfRecipeUrlIsExcludedJob::dispatch($sourceRecipeUrl);
            }
        }

        return Action::message($fields->run_sync ? 'Checked synchronously.' : 'Checking started.');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Boolean::make('Run synchronously', 'run_sync')
                ->trueValue(1)
                ->falseValue(0)
                ->help('If enabled, the job will run immediately in the current process.'),
        ];
    }
}
