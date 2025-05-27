<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\RecipeCuisine as CuisineModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class RecipeCuisine extends Resource
{
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

            Number::make('Recipes count', function () {
                return $this->recipes()->count();
            }),

            BelongsToMany::make('Recipes', 'recipes', Recipe::class),
        ];
    }
}
