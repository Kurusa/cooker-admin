<?php

namespace App\Nova\Ingredient;

use App\Models\Ingredient\IngredientUnit as IngredientUnitModel;
use App\Nova\Recipe\RecipeIngredient;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;

class IngredientUnit extends Resource
{
    public static string $model = IngredientUnitModel::class;

    public static $group = 'Ingredients';

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Ingredient', 'ingredient', Ingredient::class)
                ->rules('required')
                ->sortable(),

            BelongsTo::make('Unit', 'unit', Unit::class)
                ->rules('required'),

            HasMany::make('Usages', 'recipeIngredients', RecipeIngredient::class),
        ];
    }
}
