<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\RecipeIngredient as RecipeIngredientModel;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class RecipeIngredient extends Resource
{
    public static string $model = RecipeIngredientModel::class;

    public static $perPageViaRelationship = 50;

    public static $title = 'id';

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Recipe', 'recipe', Recipe::class),

            Text::make('Ingredient', function () {
                return $this->ingredientUnit?->ingredient?->title ?? '—';
            }),

            Number::make('Quantity'),

            Text::make('Unit', function () {
                return $this->ingredientUnit?->unit?->title ?? '—';
            }),
        ];
    }
}
