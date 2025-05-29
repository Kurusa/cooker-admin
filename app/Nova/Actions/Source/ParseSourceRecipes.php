<?php

namespace App\Nova\Actions\Source;

use App\Jobs\ParseSourceRecipesJob;
use App\Models\Source\Source;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
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
        /** @var Source $source */
        $source = $models->first();

        ParseSourceRecipesJob::dispatch($source);

        return Action::message("Parsing for {$source->title} started.");
    }
}
