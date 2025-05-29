<?php

namespace App\Nova\Actions\Source;

use App\Enums\AiProvider;
use App\Jobs\ParseSourceRecipeUrlJob;
use App\Models\Recipe\Recipe;
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
        foreach ($models as $model) {
            $recipeUrlId = $model instanceof Recipe
                ? $model->sourceRecipeUrl->id
                : $model->id;

            if ($fields->run_sync) {
                ParseSourceRecipeUrlJob::dispatchSync($recipeUrlId, AiProvider::DEEPSEEK);
            } else {
                ParseSourceRecipeUrlJob::dispatch($recipeUrlId, AiProvider::DEEPSEEK);
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
