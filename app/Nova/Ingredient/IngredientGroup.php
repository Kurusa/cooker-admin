<?php

namespace App\Nova\Ingredient;

use App\Models\Ingredient\IngredientGroup as IngredientGroupModel;
use App\Nova\Recipe\RecipeIngredient;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;
use Lupennat\ExpandableMany\HasExpandableMany;

class IngredientGroup extends Resource
{
    use HasExpandableMany;

    public static string $model = IngredientGroupModel::class;

    public static $displayInNavigation = false;

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title', 'title')
                ->nullable(),

            HasMany::make('Ingredients', 'ingredients', RecipeIngredient::class)
                ->expandable(function (HasMany $field, $resource) {
                    $field->withMeta([
                        'expandableShowLabel' => 'Show ingredients (' . $resource->ingredients()->count() . ')',
                        'expandableHideLabel' => 'Hide ingredients',
                    ]);
                }),
        ];
    }
}
