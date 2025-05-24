<?php

namespace App\Nova\Actions\Source;

use App\Models\Recipe\Recipe;
use App\Models\Source\Source;
use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

class ParseSourceRecipes extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Parse source recipes';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var Source $model */
        foreach ($models as $model) {
            Artisan::call('parse:source:recipes', [
                'sourceId' => $model->id,
            ]);
        }

        return Action::message('Parsing started.');
    }
}
