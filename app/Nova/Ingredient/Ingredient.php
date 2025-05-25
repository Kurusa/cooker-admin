<?php

namespace App\Nova\Ingredient;

use App\Models\Ingredient\Ingredient as IngredientModel;
use App\Nova\Recipe\RecipeIngredient;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Tabs\Tab;

class Ingredient extends Resource
{
    public static string $model = IngredientModel::class;

    public static $group = 'Ingredients';

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            Tab::group('Relations', [
                HasMany::make('Ingredient Units', 'units', IngredientUnit::class),
                HasMany::make('Recipe Ingredient Usages', 'recipeIngredients', RecipeIngredient::class),
            ]),
        ];
    }
}
