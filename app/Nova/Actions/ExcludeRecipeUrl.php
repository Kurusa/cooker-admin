<?php

namespace App\Nova\Actions;

use App\Models\Recipe\Recipe;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

class ExcludeRecipeUrl extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Exclude recipe url';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var Recipe $model */
        foreach ($models as $model) {
            $model->sourceRecipeUrl->is_excluded = true;
            $model->sourceRecipeUrl->save();
        }

        return Action::message('Excluded recipe url.');
    }
}
