<?php

namespace App\Nova\Actions;

use App\Models\Recipe\RecipeCuisine;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class MergeRecipeCuisines extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $name = 'Merge into...';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse|Action
    {
        /** @var RecipeCuisine $model */
        $model = $models->first();

        $targetCuisineId = $fields->get('target_cuisine');

        if (!$targetCuisineId) {
            return Action::danger('Please select a target cuisine.');
        }

        if ($model->id == $targetCuisineId) {
            return Action::danger('Cannot merge a cuisine into itself.');
        }

        $recipeIds = DB::table('recipe_cuisines_map')
            ->where('cuisine_id', $model->id)
            ->pluck('recipe_id');

        foreach ($recipeIds as $recipeId) {
            $alreadyExists = DB::table('recipe_cuisines_map')
                ->where('recipe_id', $recipeId)
                ->where('cuisine_id', $targetCuisineId)
                ->exists();

            if (!$alreadyExists) {
                DB::table('recipe_cuisines_map')->insert([
                    'recipe_id' => $recipeId,
                    'cuisine_id' => $targetCuisineId,
                ]);
            }
        }

        DB::table('recipe_cuisines_map')->where('cuisine_id', $model->id)->delete();
        $model->delete();

        return Action::message('Cuisines successfully merged.');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Select::make('Target cuisine', 'target_cuisine')
                ->options(RecipeCuisine::pluck('title', 'id'))
                ->rules('required')
                ->searchable()
                ->displayUsingLabels(),
        ];
    }
}
