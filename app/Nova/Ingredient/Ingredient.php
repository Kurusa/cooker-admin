<?php

namespace App\Nova\Ingredient;

use App\Models\Ingredient as IngredientModel;
use App\Nova\Recipe\RecipeIngredient;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Ingredient extends Resource
{
    public static string $model = IngredientModel::class;

    public static $title = 'title';

    public static $group = 'Ingredients';

    public static $search = [
        'id',
        'title',
        'original_title',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Original title')
                ->sortable()
                ->rules('nullable', 'max:255')
                ->onlyOnDetail(),

            HasMany::make('Ingredient units', 'units', IngredientUnit::class),

            HasMany::make('Recipe Ingredient Usages', 'recipeIngredients', RecipeIngredient::class)
                ->onlyOnDetail(),
        ];
    }
}
