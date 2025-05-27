<?php

namespace App\Nova\Actions;

use App\Models\Recipe\RecipeCategory;
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

class MergeRecipeCategories extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $name = 'Merge into...';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse|Action
    {
        /** @var RecipeCategory $model */
        $model = $models->first();

        $targetCategoryId = $fields->get('target_category');

        if (!$targetCategoryId) {
            return Action::danger('Please select a target category.');
        }

        if ($model->id == $targetCategoryId) {
            return Action::danger('Cannot merge a category into itself.');
        }

        $recipeIds = $model->recipes()->pluck('id');

        foreach ($recipeIds as $recipeId) {
            $alreadyExists = DB::table('recipe_categories_map')
                ->where('recipe_id', $recipeId)
                ->where('category_id', $targetCategoryId)
                ->exists();

            if (!$alreadyExists) {
                DB::table('recipe_categories_map')->insert([
                    'recipe_id' => $recipeId,
                    'category_id' => $targetCategoryId,
                ]);
            }
        }

        DB::table('recipe_categories_map')->where('category_id', $model->id)->delete();
        $model->delete();

        return Action::message('Categories successfully merged.');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Select::make('Target category', 'target_category')
                ->options(RecipeCategory::pluck('title', 'id'))
                ->rules('required')
                ->searchable()
                ->displayUsingLabels(),
        ];
    }
}
