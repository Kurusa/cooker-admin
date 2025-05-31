<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\RecipeCuisine as CuisineModel;
use App\Nova\Actions\MergeRecipeCuisines;
use App\Nova\Actions\Source\VerifySourceRecipeUrl;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Lupennat\ExpandableMany\HasExpandableMany;

class RecipeCuisine extends Resource
{
    use HasExpandableMany;

    public static string $model = CuisineModel::class;

    public static $search = [
        'title',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable(),

            BelongsToMany::make('Recipes', 'recipes', Recipe::class)
                ->expandable(function (BelongsToMany $field, $resource) {
                    $resource->loadCount('recipes');
                    $field->withMeta([
                        'expandableShowLabel' => 'Show ' . $resource->recipes()->count() . ' recipes',
                    ]);
                }),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new MergeRecipeCuisines,
        ];
    }
}
