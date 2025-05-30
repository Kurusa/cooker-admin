<?php

namespace App\Nova\Ingredient;

use App\Models\Unit as UnitModel;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Unit extends Resource
{
    public static string $model = UnitModel::class;

    public static $title = 'title';

    public static $search = [
        'id',
        'title',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Ingredients Count', function () {
                return $this->ingredients()->count();
            }),

            HasMany::make('Ingredient Units', 'ingredientUnits', IngredientUnit::class),
        ];
    }
}
