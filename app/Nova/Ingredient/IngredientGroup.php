<?php

namespace App\Nova\Ingredient;

use App\Models\Ingredient\IngredientGroup as IngredientGroupModel;
use App\Nova\Recipe\RecipeIngredient;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class IngredientGroup extends Resource
{
    public static string $model = IngredientGroupModel::class;

    public static $group = 'Ingredients';

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title', 'title')
                ->nullable(),

            HasMany::make('Ingredients', 'ingredients', RecipeIngredient::class),
        ];
    }
}
