<?php

namespace App\Nova\Actions\Source;

use App\Models\Recipe\Recipe;
use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

class ParseRecipeByUrl extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Parse recipe by url';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var SourceRecipeUrl|Recipe $model */
        foreach ($models as $model) {
            Artisan::call('parse:recipe:url', [
                'url' => $model->url,
            ]);
        }

        return Action::message('Parsing started.');
    }
}
