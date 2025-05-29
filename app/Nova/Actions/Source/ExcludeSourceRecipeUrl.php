<?php

namespace App\Nova\Actions\Source;

use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

class ExcludeSourceRecipeUrl extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Exclude URL';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var SourceRecipeUrl $model */
        foreach ($models as $model) {
            if ($model->is_excluded) {
                $model->excludedRule->delete();
            } else {
                $model->exclude();
            }
        }

        return Action::message('URLs were excluded.');
    }
}
