<?php

namespace App\Nova\Ingredient;

use App\Models\Ingredient\Ingredient as IngredientModel;
use App\Nova\Recipe\RecipeIngredient;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Lupennat\ExpandableMany\HasExpandableMany;

class Ingredient extends Resource
{
    use HasExpandableMany;

    public static string $model = IngredientModel::class;

    public static $group = 'Ingredients';

    public static $search = [
        'title',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            HasMany::make('Ingredient units', 'units', IngredientUnit::class)
                ->expandable()
                ->withMeta([
                    'expandableShowLabel' => 'Show units',
                    'expandableHideLabel' => 'Hide units',
                ]),

            HasMany::make('Recipe ingredient usages', 'recipeIngredients', RecipeIngredient::class),
        ];
    }
}
