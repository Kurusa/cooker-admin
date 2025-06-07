<?php

namespace App\Nova\Ingredient;

use App\Models\Recipe\Recipe;
use App\Nova\Recipe\Recipe as RecipeModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Resource;

class UsedInRecipe extends Resource
{
    public static string $model = Recipe::class;

    public static $displayInNavigation = false;

    public function fields(Request $request): array
    {
        return [
            BelongsTo::make('Recipe', 'recipe', RecipeModel::class),

            Number::make('Quantity', fn() => $this->pivot?->quantity)
                ->onlyOnIndex()
                ->sortable(),
        ];
    }
}
