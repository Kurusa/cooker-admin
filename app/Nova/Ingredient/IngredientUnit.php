<?php

namespace App\Nova\Ingredient;

use App\Models\IngredientUnit as IngredientUnitModel;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;

class IngredientUnit extends Resource
{
    public static string $model = IngredientUnitModel::class;

    public static $search = ['id'];

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
        ];
    }
}
