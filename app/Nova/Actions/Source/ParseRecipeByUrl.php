<?php

namespace App\Nova\Actions\Source;

use App\Jobs\SourceRecipes\ParseSourceRecipeUrlJob;
use App\Models\Recipe\Recipe;
use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;

class ParseRecipeByUrl extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Parse recipe by url';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var Recipe|SourceRecipeUrl $model */
        foreach ($models as $model) {
            $sourceRecipeUrl = $model instanceof Recipe
                ? $model->sourceRecipeUrl
                : $model;

            if ($fields->run_sync) {
                ParseSourceRecipeUrlJob::dispatchSync($sourceRecipeUrl);
            } else {
                ParseSourceRecipeUrlJob::dispatch($sourceRecipeUrl);
            }
        }

        return Action::message($fields->run_sync ? 'Parsed synchronously.' : 'Parsing started.');
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
