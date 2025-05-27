<?php

namespace App\Nova;

use App\Models\Cuisine as CuisineModel;
use App\Nova\Recipe\Recipe;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class Cuisine extends Resource
{
    public static string $model = CuisineModel::class;

    public static $title = 'title';

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
